<?php

namespace Database\Seeders;

use App\Constants\ManageStatus;
use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class LoginLockoutEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $act = 'LOGIN_SECURITY_ALERT';
        $subject = 'Security Alert: Your Account Has Been Temporarily Locked';
        $body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">{{site_name}}</h1>
            </div>
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h2 style="color: #856404; margin: 0 0 15px 0;">Account Temporarily Locked</h2>
                <p style="color: #333; line-height: 1.6; margin: 0 0 10px 0;">
                    Hello {{name}},
                </p>
                <p style="color: #333; line-height: 1.6; margin: 0 0 10px 0;">
                    Your account was locked after <strong>{{attempts}}</strong> failed login attempts.
                    Access will be restored automatically after <strong>{{lock_minutes}}</strong> minutes,
                    or once the lock expires at <strong>{{blocked_until}}</strong>.
                </p>
                <p style="color: #333; line-height: 1.6; margin: 0;">
                    If this was not you, please reset your password and contact support immediately.
                </p>
            </div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 14px; color: #555;">
                <strong>Attempt details</strong><br>
                IP: {{ip}}<br>
                Browser: {{browser}}<br>
                Device: {{operating_system}}
            </div>
        </div>';

        $shortcodes = [
            'name'             => 'User full name',
            'attempts'         => 'Maximum failed attempts before lock',
            'lock_minutes'     => 'Lock duration in minutes',
            'blocked_until'    => 'Date/time when lock expires',
            'ip'               => 'IP address of failed attempt',
            'browser'          => 'Browser name',
            'operating_system' => 'Operating system',
        ];

        $template = NotificationTemplate::where('act', $act)->first();

        if ($template) {
            $template->update([
                'subj'       => $subject,
                'email_body' => $body,
                'shortcodes' => json_encode($shortcodes),
                'email_status' => ManageStatus::ACTIVE,
            ]);
        } else {
            NotificationTemplate::create([
                'act'          => $act,
                'name'         => 'Login Security Alert',
                'subj'         => $subject,
                'email_body'   => $body,
                'shortcodes'   => json_encode($shortcodes),
                'email_status' => ManageStatus::ACTIVE,
                'sms_status'   => ManageStatus::INACTIVE,
            ]);
        }
    }
}
