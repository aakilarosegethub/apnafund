<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteData;
use App\Constants\ManageStatus;

class ReportFundraiserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create initial report fundraiser content
        SiteData::updateOrCreate(
            ['data_key' => 'report_fundraiser.content'],
            [
                'data_info' => [
                    'title' => 'Report a Fundraiser',
                    'content' => '<h2>How to Report a Fundraiser</h2>
                    <p>If you believe a fundraiser violates our terms of service or contains inappropriate content, please report it to us. We take all reports seriously and will investigate promptly.</p>
                    
                    <h3>What to Report</h3>
                    <ul>
                        <li>Fraudulent or misleading information</li>
                        <li>Inappropriate or offensive content</li>
                        <li>Violation of our community guidelines</li>
                        <li>Suspicious activity or behavior</li>
                        <li>Spam or duplicate campaigns</li>
                    </ul>
                    
                    <h3>How to Report</h3>
                    <p>To report a fundraiser, please contact us through our <a href="/contact">contact form</a> and include:</p>
                    <ul>
                        <li>Campaign URL or name</li>
                        <li>Reason for reporting</li>
                        <li>Any supporting evidence</li>
                    </ul>
                    
                    <h3>What Happens Next</h3>
                    <p>Our team will review your report within 24-48 hours and take appropriate action if necessary. We will keep your report confidential and may not be able to provide specific details about our investigation.</p>
                    
                    <p><strong>Thank you for helping us maintain a safe and trustworthy platform!</strong></p>',
                    'status' => ManageStatus::ACTIVE,
                ]
            ]
        );
    }
}

