<?php

namespace App\Models;

use App\Services\FcmPushService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'click_url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::created(function (self $notification): void {
            try {
                $data = [
                    'type' => 'user_notification',
                    'notification_id' => (string) $notification->id,
                    'click_url' => (string) ($notification->click_url ?? ''),
                ];
                app(FcmPushService::class)->notifyUserDevices(
                    (int) $notification->user_id,
                    (string) $notification->title,
                    (string) ($notification->body ?? ''),
                    $data
                );
            } catch (\Throwable $e) {
                Log::warning('UserNotification FCM: '.$e->getMessage());
            }
        });
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public static function notifyCreatorNewDonation(int $creatorUserId, Deposit $deposit, Campaign $campaign): void
    {
        if ($creatorUserId <= 0) {
            return;
        }

        $donorLabel = trim((string) ($deposit->full_name ?? ''));
        if ($donorLabel === '' && $deposit->user_id) {
            $donorLabel = (string) optional($deposit->user)->fullname;
        }
        if ($donorLabel === '') {
            $donorLabel = __('Someone');
        }

        $sym = optional(bs())->cur_sym ?? '$';
        $amountLabel = $sym.showAmount($deposit->amount);

        static::create([
            'user_id' => $creatorUserId,
            'title' => __('New contribution'),
            'body' => __(':donor contributed :amount to :campaign', [
                'donor' => \Illuminate\Support\Str::limit($donorLabel, 48),
                'amount' => $amountLabel,
                'campaign' => \Illuminate\Support\Str::limit($campaign->name, 60),
            ]),
            'click_url' => route('user.campaign.edit.basics', $campaign->slug),
        ]);
    }

    public static function notifyCampaignApproved(int $creatorUserId, Campaign $campaign): void
    {
        if ($creatorUserId <= 0) {
            return;
        }

        static::create([
            'user_id' => $creatorUserId,
            'title' => __('Campaign approved'),
            'body' => __('Congratulations! ":name" is approved and visible to supporters.', ['name' => \Illuminate\Support\Str::limit($campaign->name, 80)]),
            'click_url' => route('campaign.show', $campaign->slug),
        ]);
    }

    public static function notifyCampaignRejected(int $creatorUserId, Campaign $campaign): void
    {
        if ($creatorUserId <= 0) {
            return;
        }

        static::create([
            'user_id' => $creatorUserId,
            'title' => __('Campaign not approved'),
            'body' => __('Your campaign ":name" was not approved. You can edit and resubmit, or contact support if you need help.', [
                'name' => \Illuminate\Support\Str::limit($campaign->name, 80),
            ]),
            'click_url' => route('user.campaign.edit.basics', $campaign->slug),
        ]);
    }

    /**
     * New review/comment submitted (usually pending admin moderation).
     */
    public static function notifyCreatorReviewPending(int $creatorUserId, Campaign $campaign, string $reviewerName): void
    {
        if ($creatorUserId <= 0) {
            return;
        }

        static::create([
            'user_id' => $creatorUserId,
            'title' => __('New comment awaiting review'),
            'body' => __(':name left a comment on ":campaign". It will appear after admin approval.', [
                'name' => \Illuminate\Support\Str::limit(trim($reviewerName) ?: __('Someone'), 48),
                'campaign' => \Illuminate\Support\Str::limit($campaign->name, 60),
            ]),
            'click_url' => route('campaign.show', $campaign->slug).'?tab=comments',
        ]);
    }

    /**
     * Comment is live on the campaign page (admin approved or API posted as approved).
     */
    public static function notifyCreatorReviewPublished(int $creatorUserId, Campaign $campaign, string $reviewerName): void
    {
        if ($creatorUserId <= 0) {
            return;
        }

        static::create([
            'user_id' => $creatorUserId,
            'title' => __('New comment on your campaign'),
            'body' => __(':name commented on ":campaign".', [
                'name' => \Illuminate\Support\Str::limit(trim($reviewerName) ?: __('Someone'), 48),
                'campaign' => \Illuminate\Support\Str::limit($campaign->name, 60),
            ]),
            'click_url' => route('campaign.show', $campaign->slug).'?tab=comments',
        ]);
    }

    /** Comment on a published campaign update (API / approved). */
    public static function notifyCreatorUpdateCommentPublished(int $creatorUserId, Campaign $campaign, string $reviewerName, string $updateTitle = ''): void
    {
        if ($creatorUserId <= 0) {
            return;
        }

        $name = \Illuminate\Support\Str::limit(trim($reviewerName) ?: __('Someone'), 48);
        $camp = \Illuminate\Support\Str::limit($campaign->name, 55);
        if (trim($updateTitle) !== '') {
            $body = __(':name commented on ":update" (:campaign).', [
                'name' => $name,
                'update' => \Illuminate\Support\Str::limit($updateTitle, 50),
                'campaign' => $camp,
            ]);
        } else {
            $body = __(':name commented on an update for ":campaign".', [
                'name' => $name,
                'campaign' => $camp,
            ]);
        }

        static::create([
            'user_id' => $creatorUserId,
            'title' => __('New comment on a campaign update'),
            'body' => $body,
            'click_url' => route('campaign.show', $campaign->slug).'?tab=updates',
        ]);
    }

    /**
     * Inbox / Firestore chat: notify the other participant (bell + FCM).
     */
    public static function notifyInboxMessage(
        int $recipientUserId,
        int $senderUserId,
        string $senderDisplayName,
        string $messagePreview,
        ?int $campaignId = null,
        ?string $campaignTitle = null
    ): void {
        if ($recipientUserId <= 0 || $senderUserId <= 0 || $recipientUserId === $senderUserId) {
            return;
        }

        $preview = trim($messagePreview) !== ''
            ? \Illuminate\Support\Str::limit($messagePreview, 120, '…')
            : __('You have a new inbox message.');
        $name = \Illuminate\Support\Str::limit(trim($senderDisplayName) !== '' ? $senderDisplayName : __('Someone'), 48);

        $query = ['start' => $senderUserId];
        if ($campaignId !== null && $campaignId > 0) {
            $query['campaign_id'] = $campaignId;
        }
        if ($campaignTitle !== null && trim($campaignTitle) !== '') {
            $query['campaign_title'] = $campaignTitle;
        }

        static::create([
            'user_id' => $recipientUserId,
            'title' => __('New message from :name', ['name' => $name]),
            'body' => $preview,
            'click_url' => route('user.inbox.index', $query),
        ]);
    }
}
