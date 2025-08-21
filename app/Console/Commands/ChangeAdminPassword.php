<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ChangeAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:change-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change admin password by email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $this->error("Admin with email '{$email}' not found!");
            return 1;
        }

        $admin->password = Hash::make($password);
        $admin->save();

        $this->info("Password changed successfully for admin: {$email}");
        return 0;
    }
} 