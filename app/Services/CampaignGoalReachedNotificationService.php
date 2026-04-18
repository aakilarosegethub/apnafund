<?php

namespace App\Services;

use App\Constants\ManageStatus;
use App\Models\Campaign;
use App\Models\NotificationTemplate;
use App\Models\User;

/**
 * Sends one-time “campaign goal reached” emails to site admins and the creator when `raised_amount`
 * meets `goal_amount`. Idempotency is enforced via `campaigns.goal_reached_notified_at` and a conditional update.
 */
class CampaignGoalReachedNotificationService
{
    /**
     * After a successful donation updates `raised_amount`: if the goal is met, notify admin + creator once.
     *
     * @param  \App\Models\Campaign  $campaign  Fresh campaign instance (will be refreshed)
     */
    public function handleAfterCampaignUpdate(Campaign $campaign): void
    {
        $campaign->refresh();

        if ($campaign->goal_reached_notified_at) {
            return;
        }

        $goal = (float) $campaign->goal_amount;
        $raised = (float) $campaign->raised_amount;

        if ($goal <= 0 || $raised + 0.00001 < $goal) {
            return;
        }

        $updated = Campaign::query()
            ->where('id', $campaign->id)
            ->whereNull('goal_reached_notified_at')
            ->update(['goal_reached_notified_at' => now()]);

        if ($updated < 1) {
            return;
        }

        $campaign->refresh();
        $campaign->loadMissing('user');

        $this->sendAdminEmails($campaign);
        $this->sendCreatorEmail($campaign);
    }

    /**
     * @return array<string, string> Template shortcodes for email bodies
     */
    protected function shortCodes(Campaign $campaign): array
    {
        $setting = bs();
        $creator = $campaign->user;

        return [
            'campaign_name'   => (string) $campaign->name,
            'campaign_url'    => route('campaign.show', $campaign->slug),
            'goal_amount'     => showAmount($campaign->goal_amount),
            'raised_amount'   => showAmount($campaign->raised_amount),
            'creator_name'    => $creator ? (string) ($creator->fullname ?? $creator->username ?? '') : '',
            'creator_email'   => $creator ? (string) ($creator->email ?? '') : '',
            'admin_url'       => urlPath('admin.campaigns.index'),
        ];
    }

    /**
     * Dispatches `CAMPAIGN_GOAL_REACHED_ADMIN` to all site admins when configured.
     */
    protected function sendAdminEmails(Campaign $campaign): void
    {
        $template = NotificationTemplate::where('act', 'CAMPAIGN_GOAL_REACHED_ADMIN')
            ->where('email_status', ManageStatus::ACTIVE)
            ->first();

        if (! $template) {
            \Log::warning('CAMPAIGN_GOAL_REACHED_ADMIN template missing or inactive; goal reached email skipped for admins.');

            return;
        }

        $shortCodes = $this->shortCodes($campaign);

        try {
            notifySiteAdmins('CAMPAIGN_GOAL_REACHED_ADMIN', $shortCodes, ['email']);
        } catch (\Throwable $e) {
            \Log::error('CAMPAIGN_GOAL_REACHED_ADMIN send failed: ' . $e->getMessage());
        }
    }

    /**
     * Sends `CAMPAIGN_GOAL_REACHED_CREATOR` to the campaign owner when template is active.
     */
    protected function sendCreatorEmail(Campaign $campaign): void
    {
        $template = NotificationTemplate::where('act', 'CAMPAIGN_GOAL_REACHED_CREATOR')
            ->where('email_status', ManageStatus::ACTIVE)
            ->first();

        if (! $template) {
            \Log::warning('CAMPAIGN_GOAL_REACHED_CREATOR template missing or inactive; goal reached email skipped for creator.');

            return;
        }

        $user = $campaign->user;
        if (! $user instanceof User || empty($user->email) || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $shortCodes = $this->shortCodes($campaign);

        try {
            notify($user, 'CAMPAIGN_GOAL_REACHED_CREATOR', $shortCodes, ['email']);
        } catch (\Throwable $e) {
            \Log::error('CAMPAIGN_GOAL_REACHED_CREATOR send failed: ' . $e->getMessage());
        }
    }
}
