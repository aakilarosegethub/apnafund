<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Campaign;
use App\Models\UserNotification;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    public function __construct(
        protected FirebaseService $firebase
    ) {}

    /**
     * Inbox page - real-time chat with Firestore (UI loads in view).
     */
    public function inbox(Request $request)
    {
        $user = auth()->user();
        $inboxContent = getSiteData('inbox.content', true);
        $dataInfo = $inboxContent && $inboxContent->data_info
            ? (is_array($inboxContent->data_info) ? $inboxContent->data_info : (array) $inboxContent->data_info)
            : [];
        $pageTitle = __($dataInfo['page_title'] ?? 'Inbox');

        $firebaseConfig = $this->getFirebaseClientConfig();
        $startCreatorId = $request->query('start'); // creator user id - open/start chat with this user
        $startCampaignId = $request->query('campaign_id');
        $startCampaignSlug = $request->query('campaign_slug');
        $startCampaignTitle = $request->query('campaign_title');
        $creatorNameFromUrl = $request->query('creator_name');

        $currentUserImageUrl = getImage(getFilePath('userProfile') . '/' . ($user->image ?? $user->avatar ?? ''), getFileSize('userProfile'), true);
        $creatorImageUrl = null;
        $creatorFullname = null;
        if ($startCreatorId) {
            $creator = User::find($startCreatorId);
            if ($creator) {
                $creatorImageUrl = getImage(getFilePath('userProfile') . '/' . ($creator->image ?? $creator->avatar ?? ''), getFileSize('userProfile'), true);
                $dbName = trim($creator->fullname ?? $creator->username ?? $creator->name ?? '');
                $creatorFullname = $dbName ?: (trim($creatorNameFromUrl ?? '') ?: null);
            }
        }
        if (empty($startCampaignTitle) && $startCampaignId) {
            $campaign = Campaign::find($startCampaignId);
            if ($campaign) {
                $startCampaignTitle = $campaign->title ?? null;
            }
        }

        $inboxLabels = [
            'empty_state_message' => $dataInfo['empty_state_message'] ?? 'Select a conversation from the list, or browse campaigns to contact a creator.',
            'browse_button_text' => $dataInfo['browse_button_text'] ?? 'Browse Campaigns',
            'message_placeholder' => $dataInfo['message_placeholder'] ?? 'Type a message...',
            'send_button_text' => $dataInfo['send_button_text'] ?? 'Send',
        ];

        $viewName = $this->activeTheme . 'user.page.inbox';
        if (!view()->exists($viewName)) {
            $viewName = 'themes.green.user.page.inbox';
        }
        return view($viewName, compact(
            'pageTitle',
            'user',
            'firebaseConfig',
            'startCreatorId',
            'startCampaignId',
            'startCampaignSlug',
            'startCampaignTitle',
            'creatorFullname',
            'inboxLabels',
            'currentUserImageUrl',
            'creatorImageUrl'
        ));
    }

    /**
     * Return Firebase custom token for current user (for Firestore auth in browser).
     */
    public function getFirebaseToken(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $uid = (string) $user->id;
        $result = $this->firebase->createCustomTokenForUser($uid, [
            'email' => $user->email,
            'username' => $user->username ?? $user->fullname,
        ]);

        if (!$result['success']) {
            Log::warning('Chat: Firebase token failed', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to create token',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $result['token'],
            'uid' => $uid,
        ]);
    }

    /**
     * Unread chat count for nav badge.
     */
    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['count' => 0], 401);
        }
        try {
            $count = $this->firebase->getChatUnreadCount((string) $user->id);
            return response()->json(['count' => $count]);
        } catch (\Throwable $e) {
            Log::debug('Chat unread count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * After a Firestore message is sent from the inbox UI, create an in-app (+ FCM) notification for the recipient.
     */
    public function notifyMessageRecipient(Request $request): JsonResponse
    {
        $sender = auth()->user();
        if (!$sender) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'recipient_id' => ['required', 'integer', 'min:1', Rule::notIn([(int) $sender->id])],
            'message_preview' => 'nullable|string|max:500',
            'campaign_id' => 'nullable|integer|min:1',
            'campaign_title' => 'nullable|string|max:255',
        ]);

        $recipient = User::query()
            ->whereKey((int) $validated['recipient_id'])
            ->where('status', 1)
            ->first();

        if (!$recipient) {
            return response()->json(['success' => false, 'message' => 'Recipient not found'], 422);
        }

        $senderName = trim(implode(' ', array_filter([$sender->firstname ?? '', $sender->lastname ?? ''])));
        if ($senderName === '') {
            $senderName = (string) ($sender->username ?? $sender->name ?? $sender->email ?? '');
        }

        try {
            UserNotification::notifyInboxMessage(
                (int) $recipient->id,
                (int) $sender->id,
                $senderName,
                (string) ($validated['message_preview'] ?? ''),
                isset($validated['campaign_id']) ? (int) $validated['campaign_id'] : null,
                isset($validated['campaign_title']) ? (string) $validated['campaign_title'] : null
            );
        } catch (\Throwable $e) {
            Log::warning('Inbox notifyMessageRecipient: ' . $e->getMessage(), ['sender' => $sender->id]);

            return response()->json(['success' => false, 'message' => 'Could not create notification'], 500);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get creator names for given user IDs (for fixing "Creator" in conversation list).
     */
    public function getCreatorNames(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([], 401);
        }
        $ids = $request->query('ids', '');
        $ids = array_filter(array_map('intval', explode(',', $ids)));
        if (empty($ids)) {
            return response()->json([]);
        }
        $users = User::whereIn('id', $ids)->get(['id', 'firstname', 'lastname', 'username', 'name']);
        $result = [];
        foreach ($users as $u) {
            $name = trim($u->fullname ?? $u->username ?? $u->name ?? '');
            if ($name) {
                $result[(string) $u->id] = $name;
            }
        }
        return response()->json($result);
    }

    /**
     * Public Firebase client config (safe to expose to frontend).
     */
    protected function getFirebaseClientConfig(): array
    {
        $prefix = config('firebase.firestore.collection_prefix', 'apnacrowdfunding');
        $chat = config('firebase.firestore.chat', []);
        return [
            'apiKey' => config('firebase.client.api_key'),
            'authDomain' => config('firebase.client.auth_domain'),
            'projectId' => config('firebase.client.project_id'),
            'storageBucket' => config('firebase.client.storage_bucket'),
            'messagingSenderId' => config('firebase.client.messaging_sender_id'),
            'appId' => config('firebase.client.app_id'),
            'chatCollectionPrefix' => $prefix,
            'chatFields' => [
                'messagesSubcollection' => $chat['messages_subcollection'] ?? 'messages',
                'participantsField' => $chat['participants_field'] ?? 'participants',
                'lastMessageField' => $chat['last_message_field'] ?? 'last_message',
                'lastMessageAtField' => $chat['last_message_at_field'] ?? 'last_message_at',
                'lastSenderIdField' => $chat['last_sender_id_field'] ?? 'last_sender_id',
                'messageTextField' => $chat['message_text_field'] ?? 'text',
                'messageSenderIdField' => $chat['message_sender_id_field'] ?? 'sender_id',
                'messageCreatedAtField' => $chat['message_created_at_field'] ?? 'created_at',
            ],
        ];
    }
}
