<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Form;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create KYC form
        $kycFormData = [
            'full_name' => [
                'name' => 'Full Name',
                'label' => 'full_name',
                'is_required' => 'required',
                'extensions' => '',
                'options' => [],
                'type' => 'text',
            ],
            'date_of_birth' => [
                'name' => 'Date of Birth',
                'label' => 'date_of_birth',
                'is_required' => 'required',
                'extensions' => '',
                'options' => [],
                'type' => 'text',
            ],
            'nationality' => [
                'name' => 'Nationality',
                'label' => 'nationality',
                'is_required' => 'required',
                'extensions' => '',
                'options' => [],
                'type' => 'text',
            ],
            'address' => [
                'name' => 'Address',
                'label' => 'address',
                'is_required' => 'required',
                'extensions' => '',
                'options' => [],
                'type' => 'textarea',
            ],
            'id_document' => [
                'name' => 'ID Document (Passport/National ID)',
                'label' => 'id_document',
                'is_required' => 'required',
                'extensions' => 'jpg,jpeg,png,pdf',
                'options' => [],
                'type' => 'file',
            ],
            'selfie' => [
                'name' => 'Selfie with ID Document',
                'label' => 'selfie',
                'is_required' => 'required',
                'extensions' => 'jpg,jpeg,png',
                'options' => [],
                'type' => 'file',
            ],
        ];

        Form::create([
            'act' => 'kyc',
            'form_data' => $kycFormData,
        ]);
    }
}
