<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $existingAdmin = Admin::where('email', 'admin@test.com')->first();
        if ($existingAdmin) {
            $this->command->info('Test admin already exists. Skipping...');
            return;
        }

        // Create test admin user
        Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'username' => 'testadmin',
            'contact' => '+1234567890',
            'address' => 'Test Address',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Test admin created successfully!');
        $this->command->info('Email: admin@test.com');
        $this->command->info('Password: password123');
    }
}
