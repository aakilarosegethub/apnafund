<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if settings already exist
        if (Setting::count() == 0) {
            Setting::create([
                'site_name' => 'ApnaCrowdfunding',
                'site_url' => 'http://localhost:8000',
                'site_logo' => null,
                'site_favicon' => null,
                'site_description' => 'Crowdfunding Platform',
                'site_email' => 'admin@apnacrowdfunding.com',
                'site_phone' => '+1234567890',
                'site_address' => 'Your Address Here',
                'site_maintenance' => '0',
                'site_cur' => 'USD',
                'cur_sym' => '$',
                'fraction_digit' => 2,
                'first_color' => '00ffff',
                'second_color' => 'ffff00',
                'active_theme' => 'primary',
                'signup' => true,
                'enforce_ssl' => false,
                'agree_policy' => false,
                'strong_pass' => false,
                'kc' => false,
                'ec' => false,
                'ea' => false,
                'sc' => false,
                'sa' => false,
                'language' => false,
                'per_page_item' => 20,
            ]);
        }
    }
} 