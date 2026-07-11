<?php

namespace Database\Seeders;

use App\Constants\ManageStatus;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class CampaignGoalReachedEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->createOrUpdateTemplate(
            'CAMPAIGN_GOAL_REACHED_ADMIN',
            'Fundraising goal reached — {{campaign_name}}',
            '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0 0 20px 0; text-align: center;">Campaign goal reached</h2>
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    A campaign has reached its fundraising goal. Summary:
                </p>
                <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Campaign:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{campaign_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Goal:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{goal_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Raised:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #05ce78; font-weight: bold;">{{raised_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Creator:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{creator_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold; color: #333;">Creator email:</td>
                            <td style="padding: 8px 0; color: #666;">{{creator_email}}</td>
                        </tr>
                    </table>
                </div>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{admin_url}}" style="background: #05ce78; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                        Open admin panel
                    </a>
                </div>
                <p style="color: #666; font-size: 14px;">Public page: <a href="{{campaign_url}}">{{campaign_url}}</a></p>
            </div>
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>This is an automated notification from ApnaCrowdfunding.</p>
            </div>
        </div>',
            [
                'campaign_name' => 'Campaign title',
                'campaign_url' => 'Public campaign URL',
                'goal_amount' => 'Goal amount (formatted)',
                'raised_amount' => 'Raised amount (formatted)',
                'creator_name' => 'Campaign creator name',
                'creator_email' => 'Campaign creator email',
                'admin_url' => 'Admin campaigns URL',
            ]
        );

        $this->createOrUpdateTemplate(
            'CAMPAIGN_GOAL_REACHED_CREATOR',
            'Congratulations — your campaign reached its goal: {{campaign_name}}',
            '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0 0 20px 0; text-align: center;">Your goal is complete</h2>
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    Dear {{creator_name}},
                </p>
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    Great news: your campaign <strong>{{campaign_name}}</strong> has reached its fundraising goal.
                </p>
                <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Goal:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{goal_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold; color: #333;">Raised:</td>
                            <td style="padding: 8px 0; color: #05ce78; font-weight: bold;">{{raised_amount}}</td>
                        </tr>
                    </table>
                </div>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{campaign_url}}" style="background: #05ce78; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                        View your campaign
                    </a>
                </div>
                <p style="color: #666; line-height: 1.6; margin: 20px 0 0 0; text-align: center;">
                    Thank you for using ApnaCrowdfunding.
                </p>
            </div>
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>This is an automated message from ApnaCrowdfunding.</p>
            </div>
        </div>',
            [
                'campaign_name' => 'Campaign title',
                'campaign_url' => 'Public campaign URL',
                'goal_amount' => 'Goal amount (formatted)',
                'raised_amount' => 'Raised amount (formatted)',
                'creator_name' => 'Campaign creator name',
                'creator_email' => 'Campaign creator email',
                'admin_url' => 'Admin campaigns URL',
            ]
        );

        echo "Campaign goal reached email templates created/updated successfully!\n";
    }

    private function createOrUpdateTemplate(string $act, string $subject, string $body, array $shortcodes): void
    {
        $template = NotificationTemplate::where('act', $act)->first();

        if ($template) {
            $template->update([
                'subj' => $subject,
                'email_body' => $body,
                'shortcodes' => json_encode($shortcodes),
                'email_status' => ManageStatus::ACTIVE,
                'sms_status' => ManageStatus::INACTIVE,
            ]);
            echo "Template {$act} updated successfully!\n";
        } else {
            NotificationTemplate::create([
                'act' => $act,
                'name' => ucwords(str_replace('_', ' ', $act)),
                'subj' => $subject,
                'email_body' => $body,
                'shortcodes' => json_encode($shortcodes),
                'email_status' => ManageStatus::ACTIVE,
                'sms_status' => ManageStatus::INACTIVE,
            ]);
            echo "Template {$act} created successfully!\n";
        }
    }
}
