<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;
use App\Constants\ManageStatus;

class PaymentSuccessEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Payment Success Email Template
        $this->createOrUpdateTemplate('ADMIN_PAYMENT_SUCCESS', 'New Successful Payment Received - ApnaCrowdfunding', '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0 0 20px 0; text-align: center;">New Successful Payment Received</h2>
                
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    A new successful payment has been received on your platform. Here are the details:
                </p>
                
                <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #05ce78; margin: 0 0 15px 0;">Payment Details</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Donor Name:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{full_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Donor Email:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{email}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Campaign:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{campaign_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Amount:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #05ce78; font-weight: bold;">{{currency_symbol}}{{amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Payment Method:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{method_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Transaction ID:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{trx}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold; color: #333;">Date:</td>
                            <td style="padding: 8px 0; color: #666;">{{date}}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{admin_url}}" style="background: #05ce78; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                        View in Admin Panel
                    </a>
                </div>
            </div>
            
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>This is an automated notification from ApnaCrowdfunding.</p>
                <p>© 2024 ApnaCrowdfunding. All rights reserved.</p>
            </div>
        </div>', [
            'full_name' => 'Donor full name',
            'email' => 'Donor email address',
            'campaign_name' => 'Campaign name',
            'amount' => 'Donation amount',
            'method_name' => 'Payment method name',
            'trx' => 'Transaction ID',
            'date' => 'Payment date',
            'admin_url' => 'Admin panel URL',
            'currency_symbol' => 'Currency symbol'
        ]);

        // Create User Payment Success Email Template
        $this->createOrUpdateTemplate('USER_PAYMENT_SUCCESS', 'Payment Successful - Thank You for Your Donation!', '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #05ce78; margin: 0;">ApnaCrowdfunding</h1>
                <p style="color: #666; margin: 10px 0 0 0;">Crowdfunding Platform</p>
            </div>
            
            <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #333; margin: 0 0 20px 0; text-align: center;">🎉 Payment Successful!</h2>
                
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    Dear {{full_name}},
                </p>
                
                <p style="color: #666; line-height: 1.6; margin: 0 0 20px 0;">
                    Thank you for your generous donation! Your payment has been successfully processed and your contribution is making a real difference.
                </p>
                
                <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #05ce78; margin: 0 0 15px 0;">Donation Summary</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Campaign:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{campaign_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Donation Amount:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #05ce78; font-weight: bold;">{{currency_symbol}}{{amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Payment Method:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{method_name}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #333;">Transaction ID:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #eee; color: #666;">{{trx}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-weight: bold; color: #333;">Date:</td>
                            <td style="padding: 8px 0; color: #666;">{{date}}</td>
                        </tr>
                    </table>
                </div>
                
                <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #05ce78;">
                    <h4 style="color: #05ce78; margin: 0 0 10px 0;">Your Impact</h4>
                    <p style="color: #666; margin: 0; line-height: 1.6;">
                        Your generous donation is helping to make a real difference. Every contribution, no matter the size, brings us closer to achieving the campaign goal.
                    </p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{campaign_url}}" style="background: #05ce78; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                        View Campaign Progress
                    </a>
                </div>
                
                <p style="color: #666; line-height: 1.6; margin: 20px 0 0 0; text-align: center;">
                    Thank you for being part of this meaningful cause. Your support means the world to us!
                </p>
            </div>
            
            <div style="text-align: center; color: #999; font-size: 12px;">
                <p>This is an automated receipt from ApnaCrowdfunding.</p>
                <p>© 2024 ApnaCrowdfunding. All rights reserved.</p>
            </div>
        </div>', [
            'full_name' => 'Donor full name',
            'campaign_name' => 'Campaign name',
            'amount' => 'Donation amount',
            'method_name' => 'Payment method name',
            'trx' => 'Transaction ID',
            'date' => 'Payment date',
            'campaign_url' => 'Campaign URL',
            'currency_symbol' => 'Currency symbol'
        ]);

        echo "Payment success email templates created/updated successfully!\n";
    }

    private function createOrUpdateTemplate($act, $subject, $body, $shortcodes)
    {
        $template = NotificationTemplate::where('act', $act)->first();
        
        if ($template) {
            $template->update([
                'subj' => $subject,
                'email_body' => $body,
                'shortcodes' => json_encode($shortcodes),
                'email_status' => ManageStatus::ACTIVE,
                'sms_status' => ManageStatus::INACTIVE
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
                'sms_status' => ManageStatus::INACTIVE
            ]);
            echo "Template {$act} created successfully!\n";
        }
    }
}
