<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->getPermissions();
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['key' => $perm['key']],
                [
                    'module' => $perm['module'],
                    'action' => $perm['action'],
                    'description' => $perm['description'] ?? null,
                ]
            );
        }

        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full system access',
                'is_super_admin' => true,
                'is_protected' => true,
            ]
        );

        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // Create default Content Manager role (assignable to sub-admins)
        $contentManager = Role::firstOrCreate(
            ['slug' => 'content-manager'],
            [
                'name' => 'Content Manager',
                'description' => 'Manage campaigns, categories, banners',
                'is_super_admin' => false,
                'is_protected' => false,
            ]
        );
        $contentManager->permissions()->sync(
            Permission::whereIn('key', [
                'campaigns.view', 'campaigns.update', 'categories.view', 'categories.manage',
                'banners.view', 'banners.manage', 'donations.view', 'contacts.view',
            ])->pluck('id')
        );
        $contentManager->update(['dashboard_widgets' => ['donations', 'campaigns', 'chart', 'latest_users']]);

        // Category Manager - ONLY categories (for testing minimal sidebar)
        $categoryManager = Role::firstOrCreate(
            ['slug' => 'category-manager'],
            [
                'name' => 'Category Manager',
                'description' => 'Manage categories only',
                'is_super_admin' => false,
                'is_protected' => false,
            ]
        );
        $categoryManager->permissions()->sync(
            Permission::whereIn('key', ['categories.view', 'categories.manage'])->pluck('id')
        );
        $categoryManager->update(['dashboard_widgets' => ['campaigns']]);

        // Assign Super Admin role to existing admins with full access (null or ['*'] permissions)
        $admins = \App\Models\Admin::whereNull('role_id')
            ->where(function ($q) {
                $q->whereNull('permissions')
                    ->orWhereJsonContains('permissions', '*');
            })
            ->get();
        foreach ($admins as $admin) {
            $admin->update(['role_id' => $superAdmin->id]);
        }
    }

    protected function getPermissions(): array
    {
        return [
            ['key' => 'users.view', 'module' => 'users', 'action' => 'view', 'description' => 'View users'],
            ['key' => 'users.create', 'module' => 'users', 'action' => 'create', 'description' => 'Create users'],
            ['key' => 'users.update', 'module' => 'users', 'action' => 'update', 'description' => 'Update users'],
            ['key' => 'users.delete', 'module' => 'users', 'action' => 'delete', 'description' => 'Delete users'],
            ['key' => 'users.kyc_approve', 'module' => 'users', 'action' => 'kyc_approve', 'description' => 'Approve/reject KYC'],
            ['key' => 'admin_users.view', 'module' => 'admin_users', 'action' => 'view', 'description' => 'View admin users'],
            ['key' => 'admin_users.create', 'module' => 'admin_users', 'action' => 'create', 'description' => 'Create admin users'],
            ['key' => 'admin_users.update', 'module' => 'admin_users', 'action' => 'update', 'description' => 'Update admin users'],
            ['key' => 'admin_users.delete', 'module' => 'admin_users', 'action' => 'delete', 'description' => 'Delete admin users'],
            ['key' => 'campaigns.view', 'module' => 'campaigns', 'action' => 'view', 'description' => 'View campaigns'],
            ['key' => 'campaigns.create', 'module' => 'campaigns', 'action' => 'create', 'description' => 'Create campaigns'],
            ['key' => 'campaigns.update', 'module' => 'campaigns', 'action' => 'update', 'description' => 'Update campaigns'],
            ['key' => 'campaigns.delete', 'module' => 'campaigns', 'action' => 'delete', 'description' => 'Delete campaigns'],
            ['key' => 'campaigns.approve', 'module' => 'campaigns', 'action' => 'approve', 'description' => 'Approve/reject campaigns'],
            ['key' => 'campaigns.comments_approve', 'module' => 'campaigns', 'action' => 'comments_approve', 'description' => 'Approve/reject campaign comments'],
            ['key' => 'categories.view', 'module' => 'categories', 'action' => 'view', 'description' => 'View categories'],
            ['key' => 'categories.manage', 'module' => 'categories', 'action' => 'manage', 'description' => 'Manage categories'],
            ['key' => 'banners.view', 'module' => 'banners', 'action' => 'view', 'description' => 'View banners'],
            ['key' => 'banners.manage', 'module' => 'banners', 'action' => 'manage', 'description' => 'Manage banners'],
            ['key' => 'gateways.view', 'module' => 'gateways', 'action' => 'view', 'description' => 'View gateways'],
            ['key' => 'gateways.manage', 'module' => 'gateways', 'action' => 'manage', 'description' => 'Manage gateways'],
            ['key' => 'donations.view', 'module' => 'donations', 'action' => 'view', 'description' => 'View donations'],
            ['key' => 'donations.manage', 'module' => 'donations', 'action' => 'manage', 'description' => 'Manage donations'],
            ['key' => 'donations.approve', 'module' => 'donations', 'action' => 'approve', 'description' => 'Approve/reject donations'],
            ['key' => 'withdrawals.view', 'module' => 'withdrawals', 'action' => 'view', 'description' => 'View withdrawals'],
            ['key' => 'withdrawals.manage', 'module' => 'withdrawals', 'action' => 'manage', 'description' => 'Manage withdrawals'],
            ['key' => 'withdrawals.approve', 'module' => 'withdrawals', 'action' => 'approve', 'description' => 'Approve/reject withdrawals'],
            ['key' => 'payout_banks.view', 'module' => 'payout_banks', 'action' => 'view', 'description' => 'View payout banks'],
            ['key' => 'payout_banks.manage', 'module' => 'payout_banks', 'action' => 'manage', 'description' => 'Manage payout banks'],
            ['key' => 'creator_payouts.view', 'module' => 'creator_payouts', 'action' => 'view', 'description' => 'View creator payouts'],
            ['key' => 'creator_payouts.manage', 'module' => 'creator_payouts', 'action' => 'manage', 'description' => 'Manage creator payouts'],
            ['key' => 'transactions.view', 'module' => 'transactions', 'action' => 'view', 'description' => 'View transactions'],
            ['key' => 'contacts.view', 'module' => 'contacts', 'action' => 'view', 'description' => 'View contacts'],
            ['key' => 'contacts.manage', 'module' => 'contacts', 'action' => 'manage', 'description' => 'Manage contacts'],
            ['key' => 'subscribers.view', 'module' => 'subscribers', 'action' => 'view', 'description' => 'View subscribers'],
            ['key' => 'subscribers.manage', 'module' => 'subscribers', 'action' => 'manage', 'description' => 'Manage subscribers'],
            ['key' => 'settings.view', 'module' => 'settings', 'action' => 'view', 'description' => 'View settings'],
            ['key' => 'settings.manage', 'module' => 'settings', 'action' => 'manage', 'description' => 'Manage settings'],
            ['key' => 'settings.basic', 'module' => 'settings', 'action' => 'basic', 'description' => 'Basic Settings'],
            ['key' => 'settings.home', 'module' => 'settings', 'action' => 'home', 'description' => 'Home Settings'],
            ['key' => 'settings.social_login', 'module' => 'settings', 'action' => 'social_login', 'description' => 'Social Login'],
            ['key' => 'settings.firebase_otp', 'module' => 'settings', 'action' => 'firebase_otp', 'description' => 'Firebase OTP'],
            ['key' => 'settings.gemini', 'module' => 'settings', 'action' => 'gemini', 'description' => 'Gemini AI'],
            ['key' => 'settings.plugins', 'module' => 'settings', 'action' => 'plugins', 'description' => 'Plugins'],
            ['key' => 'settings.language', 'module' => 'settings', 'action' => 'language', 'description' => 'Language'],
            ['key' => 'settings.seo', 'module' => 'settings', 'action' => 'seo', 'description' => 'SEO'],
            ['key' => 'settings.kyc', 'module' => 'settings', 'action' => 'kyc', 'description' => 'KYC Settings'],
            ['key' => 'settings.site_content', 'module' => 'settings', 'action' => 'site_content', 'description' => 'Site Content / Homepage'],
            ['key' => 'settings.themes', 'module' => 'settings', 'action' => 'themes', 'description' => 'Themes'],
            ['key' => 'settings.cookie', 'module' => 'settings', 'action' => 'cookie', 'description' => 'GDPR Cookie'],
            ['key' => 'settings.maintenance', 'module' => 'settings', 'action' => 'maintenance', 'description' => 'Maintenance'],
            ['key' => 'settings.cache', 'module' => 'settings', 'action' => 'cache', 'description' => 'Cache Clear'],
            ['key' => 'settings.customcode', 'module' => 'settings', 'action' => 'customcode', 'description' => 'Custom Code'],
            ['key' => 'settings.report', 'module' => 'settings', 'action' => 'report', 'description' => 'Report Fundraiser'],
            ['key' => 'notifications.view', 'module' => 'notifications', 'action' => 'view', 'description' => 'View notifications'],
            ['key' => 'notifications.manage', 'module' => 'notifications', 'action' => 'manage', 'description' => 'Manage notifications'],
            ['key' => 'activity_logs.view', 'module' => 'activity_logs', 'action' => 'view', 'description' => 'View activity logs'],
            ['key' => 'roles.view', 'module' => 'roles', 'action' => 'view', 'description' => 'View roles'],
            ['key' => 'roles.manage', 'module' => 'roles', 'action' => 'manage', 'description' => 'Manage roles'],
        ];
    }
}
