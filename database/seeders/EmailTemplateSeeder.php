<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;
use App\Constants\ManageStatus;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update EVER_CODE email template
        $this->createOrUpdateTemplate('EVER_CODE', 'Verify Your Email - ApnaCrowdfunding', '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0 0 20px 0; text-align: center;">Email Verification Required</h2>
                
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    Thank you for joining ApnaCrowdfunding! To complete your registration and start creating campaigns, 
                    please verify your email address using the code below:
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="background: #05ce78; color: white; font-size: 32px; font-weight: bold; 
                                padding: 20px; border-radius: 8px; letter-spacing: 5px; 
                                display: inline-block; min-width: 200px;">
                        {{code}}
                    </div>
                </div>
                
                <p style="color: #666; line-height: 1.6; margin: 20px 0 0 0; text-align: center; font-size: 14px;">
                    This code will expire in 10 minutes for security reasons.
                </p>
            </div>
            
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>If you didn\'t create an account with ApnaCrowdfunding, please ignore this email.</p>
                <p>© 2024 ApnaCrowdfunding. All rights reserved.</p>
            </div>
        </div>', [
            'code' => 'Email verification code'
        ]);

        // Create or update PASS_RESET_CODE email template
        $this->createOrUpdateTemplate('PASS_RESET_CODE', 'Password Reset Code - ApnaCrowdfunding', '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0 0 20px 0; text-align: center;">Password Reset Request</h2>
                
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    We received a request to reset your password. Use the verification code below to proceed with resetting your password:
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="background: #05ce78; color: white; font-size: 32px; font-weight: bold; 
                                padding: 20px; border-radius: 8px; letter-spacing: 5px; 
                                display: inline-block; min-width: 200px;">
                        {{code}}
                    </div>
                </div>
                
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <p style="color: #856404; margin: 0; font-size: 14px;">
                        <strong>Security Information:</strong><br>
                        • Device: {{operating_system}}<br>
                        • Browser: {{browser}}<br>
                        • IP Address: {{ip}}<br>
                        • Time: {{time}}
                    </p>
                </div>
                
                <p style="color: #666; line-height: 1.6; margin: 20px 0 0 0; text-align: center; font-size: 14px;">
                    This code will expire in 10 minutes for security reasons.
                </p>
            </div>
            
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>If you didn\'t request a password reset, please ignore this email and your password will remain unchanged.</p>
                <p>© 2024 ApnaCrowdfunding. All rights reserved.</p>
            </div>
        </div>', [
            'code' => 'Password reset verification code',
            'operating_system' => 'User operating system',
            'browser' => 'User browser',
            'ip' => 'User IP address',
            'time' => 'Request time'
        ]);

        // Create or update PASS_RESET_DONE email template
        $this->createOrUpdateTemplate('PASS_RESET_DONE', 'Password Reset Successful - ApnaCrowdfunding', '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            
            <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #155724; margin: 0 0 20px 0; text-align: center;">✓ Password Reset Successful</h2>
                
                <p style="color: #155724; line-height: 1.6; margin: 0 0 20px 0;">
                    Your password has been successfully reset. You can now log in to your account with your new password.
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{login_url}}" style="background: #05ce78; color: white; padding: 15px 30px; 
                       text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
                        Login to Your Account
                    </a>
                </div>
                
                <div style="background: #fff; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <p style="color: #155724; margin: 0; font-size: 14px;">
                        <strong>Security Information:</strong><br>
                        • Device: {{operating_system}}<br>
                        • Browser: {{browser}}<br>
                        • IP Address: {{ip}}<br>
                        • Time: {{time}}
                    </p>
                </div>
            </div>
            
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>If you didn\'t reset your password, please contact our support team immediately.</p>
                <p>© 2024 ApnaCrowdfunding. All rights reserved.</p>
            </div>
        </div>', [
            'login_url' => 'Login page URL',
            'operating_system' => 'User operating system',
            'browser' => 'User browser',
            'ip' => 'User IP address',
            'time' => 'Reset time'
        ]);

        echo "All email templates created/updated successfully!\n";
    }

    private function createOrUpdateTemplate($act, $subject, $body, $shortcodes)
    {
        $template = NotificationTemplate::where('act', $act)->first();
        
        if ($template) {
            $template->update([
                'subj' => $subject,
                'email_body' => $body,
                'shortcodes' => json_encode($shortcodes)
            ]);
            echo "Template {$act} updated successfully!\n";
        } else {
            NotificationTemplate::create([
                'act' => $act,
                'name' => ucwords(str_replace('_', ' ', $act)),
                'subj' => $subject,
                'email_body' => $body,
                'shortcodes' => json_encode($shortcodes),
                'status' => ManageStatus::YES
            ]);
            echo "Template {$act} created successfully!\n";
        }
    }
}
