<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Campaign;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        return [
            'apiKey' => config('firebase.client.api_key'),
            'authDomain' => config('firebase.client.auth_domain'),
            'projectId' => config('firebase.client.project_id'),
            'storageBucket' => config('firebase.client.storage_bucket'),
            'messagingSenderId' => config('firebase.client.messaging_sender_id'),
            'appId' => config('firebase.client.app_id'),
            'chatCollectionPrefix' => $prefix,
        ];
    }
}
