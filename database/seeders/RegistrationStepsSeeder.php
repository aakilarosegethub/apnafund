<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegistrationStep;
use App\Models\RegistrationQuestion;

class RegistrationStepsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Business Type
        $step1 = RegistrationStep::create([
            'title' => 'What type of business are you?',
            'subtitle' => 'This helps us understand your funding needs better',
            'step_order' => 1,
            'is_active' => true,
            'is_required' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step1->id,
            'field_name' => 'businessType',
            'label' => 'Business Type',
            'type' => 'select',
            'placeholder' => 'Select your business type',
            'is_required' => true,
            'order' => 1,
            'options' => [
                ['value' => 'startup', 'label' => 'Startup'],
                ['value' => 'small-business', 'label' => 'Small Business'],
                ['value' => 'nonprofit', 'label' => 'Non-Profit Organization'],
                ['value' => 'creative-project', 'label' => 'Creative Project'],
                ['value' => 'tech-company', 'label' => 'Technology Company'],
                ['value' => 'manufacturing', 'label' => 'Manufacturing'],
                ['value' => 'retail', 'label' => 'Retail Business'],
                ['value' => 'service', 'label' => 'Service Business'],
                ['value' => 'other', 'label' => 'Other']
            ],
            'is_active' => true
        ]);

        // Step 2: Business Details
        $step2 = RegistrationStep::create([
            'title' => 'Tell us about your business',
            'subtitle' => 'Share your story and vision',
            'step_order' => 2,
            'is_active' => true,
            'is_required' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step2->id,
            'field_name' => 'businessName',
            'label' => 'Business Name',
            'type' => 'text',
            'placeholder' => 'Enter your business name',
            'is_required' => true,
            'order' => 1,
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step2->id,
            'field_name' => 'businessDescription',
            'label' => 'Business Description',
            'type' => 'textarea',
            'placeholder' => 'Describe what your business does, your mission, and what makes you unique',
            'is_required' => true,
            'order' => 2,
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step2->id,
            'field_name' => 'industry',
            'label' => 'Industry',
            'type' => 'select',
            'placeholder' => 'Select your industry',
            'is_required' => true,
            'order' => 3,
            'options' => [
                ['value' => 'technology', 'label' => 'Technology'],
                ['value' => 'healthcare', 'label' => 'Healthcare'],
                ['value' => 'education', 'label' => 'Education'],
                ['value' => 'finance', 'label' => 'Finance'],
                ['value' => 'retail', 'label' => 'Retail'],
                ['value' => 'manufacturing', 'label' => 'Manufacturing'],
                ['value' => 'food-beverage', 'label' => 'Food & Beverage'],
                ['value' => 'creative-arts', 'label' => 'Creative Arts'],
                ['value' => 'environmental', 'label' => 'Environmental'],
                ['value' => 'other', 'label' => 'Other']
            ],
            'is_active' => true
        ]);

        // Step 3: Funding Details
        $step3 = RegistrationStep::create([
            'title' => 'What are your funding goals?',
            'subtitle' => 'Help us understand your financial needs',
            'step_order' => 3,
            'is_active' => true,
            'is_required' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step3->id,
            'field_name' => 'fundingAmount',
            'label' => 'Funding Amount Needed',
            'type' => 'select',
            'placeholder' => 'Select funding amount',
            'is_required' => true,
            'order' => 1,
            'options' => [
                ['value' => 'under-10k', 'label' => 'Under $10,000'],
                ['value' => '10k-50k', 'label' => '$10,000 - $50,000'],
                ['value' => '50k-100k', 'label' => '$50,000 - $100,000'],
                ['value' => '100k-500k', 'label' => '$100,000 - $500,000'],
                ['value' => '500k-1m', 'label' => '$500,000 - $1,000,000'],
                ['value' => 'over-1m', 'label' => 'Over $1,000,000']
            ],
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step3->id,
            'field_name' => 'fundUsage',
            'label' => 'How will you use the funds?',
            'type' => 'select',
            'placeholder' => 'Select primary use',
            'is_required' => true,
            'order' => 2,
            'options' => [
                ['value' => 'product-development', 'label' => 'Product Development'],
                ['value' => 'marketing', 'label' => 'Marketing & Advertising'],
                ['value' => 'equipment', 'label' => 'Equipment & Infrastructure'],
                ['value' => 'inventory', 'label' => 'Inventory & Supplies'],
                ['value' => 'expansion', 'label' => 'Business Expansion'],
                ['value' => 'research', 'label' => 'Research & Development'],
                ['value' => 'operating-costs', 'label' => 'Operating Costs'],
                ['value' => 'other', 'label' => 'Other']
            ],
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step3->id,
            'field_name' => 'campaignDuration',
            'label' => 'Campaign Duration',
            'type' => 'select',
            'placeholder' => 'Select campaign length',
            'is_required' => true,
            'order' => 3,
            'options' => [
                ['value' => '30-days', 'label' => '30 days'],
                ['value' => '60-days', 'label' => '60 days'],
                ['value' => '90-days', 'label' => '90 days'],
                ['value' => '120-days', 'label' => '120 days']
            ],
            'is_active' => true
        ]);

        // Step 4: Contact Information
        $step4 = RegistrationStep::create([
            'title' => 'Your contact information',
            'subtitle' => 'We\'ll use this to keep you updated',
            'step_order' => 4,
            'is_active' => true,
            'is_required' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step4->id,
            'field_name' => 'firstName',
            'label' => 'First Name',
            'type' => 'text',
            'placeholder' => 'Enter your first name',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step4->id,
            'field_name' => 'lastName',
            'label' => 'Last Name',
            'type' => 'text',
            'placeholder' => 'Enter your last name',
            'is_required' => true,
            'order' => 2,
            'validation_rules' => ['min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step4->id,
            'field_name' => 'phone',
            'label' => 'Phone Number',
            'type' => 'tel',
            'placeholder' => 'Enter your phone number',
            'help_text' => 'Format will be applied based on your country selection',
            'is_required' => true,
            'order' => 3,
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step4->id,
            'field_name' => 'country',
            'label' => 'Country',
            'type' => 'select',
            'placeholder' => 'Select your country',
            'is_required' => true,
            'order' => 4,
            'is_active' => true
        ]);

        // Step 5: Account Creation
        $step5 = RegistrationStep::create([
            'title' => 'Create your account',
            'subtitle' => 'Set up your login credentials',
            'step_order' => 5,
            'is_active' => true,
            'is_required' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step5->id,
            'field_name' => 'signupEmail',
            'label' => 'Email Address',
            'type' => 'email',
            'placeholder' => 'Enter your email address',
            'is_required' => true,
            'order' => 1,
            'validation_rules' => ['max:100'],
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step5->id,
            'field_name' => 'password',
            'label' => 'Password',
            'type' => 'password',
            'placeholder' => 'Create a strong password',
            'is_required' => true,
            'order' => 2,
            'validation_rules' => ['min:8', 'max:50', 'regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'],
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step5->id,
            'field_name' => 'confirmPassword',
            'label' => 'Confirm Password',
            'type' => 'password',
            'placeholder' => 'Confirm your password',
            'is_required' => true,
            'order' => 3,
            'is_active' => true
        ]);

        RegistrationQuestion::create([
            'step_id' => $step5->id,
            'field_name' => 'termsCheckbox',
            'label' => 'I agree to the Terms of Service and Privacy Policy',
            'type' => 'checkbox',
            'is_required' => true,
            'order' => 4,
            'is_active' => true
        ]);
    }
}
