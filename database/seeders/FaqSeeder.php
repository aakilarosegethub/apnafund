<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteData;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create FAQ content
        SiteData::updateOrCreate(
            ['data_key' => 'faq.content'],
            [
                'data_info' => [
                    'section_heading' => 'Frequently Asked Questions',
                    'description' => 'Find answers to common questions about our crowdfunding platform.'
                ]
            ]
        );

        // Create sample FAQ elements
        $faqElements = [
            [
                'question' => 'How do I start a campaign?',
                'answer' => 'To start a campaign, simply create an account, click on "Start Campaign", fill in your project details, set your funding goal, and submit for review. Our team will review your campaign within 24-48 hours.'
            ],
            [
                'question' => 'What are the fees for using the platform?',
                'answer' => 'We charge a small platform fee of 5% on successful campaigns. Payment processing fees are additional and vary by payment method. All fees are clearly displayed before you confirm your donation.'
            ],
            [
                'question' => 'How do I donate to a campaign?',
                'answer' => 'Browse campaigns, select one you want to support, choose your donation amount, and complete the payment process. You can donate using various payment methods including credit cards, bank transfers, and digital wallets.'
            ],
            [
                'question' => 'What happens if a campaign doesn\'t reach its goal?',
                'answer' => 'If a campaign doesn\'t reach its funding goal by the deadline, all donations are automatically refunded to the backers. Campaign creators receive nothing if the goal isn\'t met.'
            ],
            [
                'question' => 'How do I contact support?',
                'answer' => 'You can contact our support team through the contact form on our website, email us at support@apnacrowdfunding.com, or use the live chat feature available on our platform.'
            ],
            [
                'question' => 'Is my payment information secure?',
                'answer' => 'Yes, we use industry-standard encryption and security measures to protect your payment information. We never store your full payment details and work with trusted payment processors.'
            ]
        ];

        foreach ($faqElements as $index => $faq) {
            SiteData::updateOrCreate(
                ['data_key' => 'faq.element'],
                [
                    'data_info' => $faq
                ]
            );
        }
    }
}