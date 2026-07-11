<?php

namespace App\Support;

use App\Models\Admin;

class PermissionHelper
{
    /** Legacy permission key to RBAC permission key mapping (for backward compatibility) */
    protected static array $legacyMap = [
        'seo' => 'settings.manage',
        'blog' => 'campaigns.view',
        'categories' => 'categories.view',
        'banners' => 'banners.view',
        'campaigns' => 'campaigns.view',
        'users' => 'users.view',
        'gateways' => 'gateways.view',
        'donations' => 'donations.view',
        'withdrawals' => 'withdrawals.view',
        'payout_banks' => 'payout_banks.view',
        'creator_payouts' => 'creator_payouts.view',
        'transactions' => 'transactions.view',
        'store' => 'transactions.view',
        'contacts' => 'contacts.view',
        'subscribers' => 'subscribers.view',
        'basic_settings' => 'settings.basic',
        'site_settings' => 'settings.site_content',
        'notifications' => 'notifications.view',
        'plugins' => 'settings.plugins',
        'language' => 'settings.language',
        'kyc' => 'settings.kyc',
        'themes' => 'settings.themes',
        'cookie' => 'settings.cookie',
        'maintenance' => 'settings.maintenance',
        '*' => null, // Super admin - handled separately
    ];

    public function hasPermission(?Admin $admin, string $key): bool
    {
        if (! $admin) {
            return false;
        }

        if ($this->isSuperAdmin($admin)) {
            return true;
        }

        $rbacKey = self::$legacyMap[$key] ?? $key;

        if ($admin->role_id && $admin->rbacRole) {
            return $admin->rbacRole->hasPermission($rbacKey);
        }

        return $this->hasLegacyPermission($admin, $key);
    }

    public function isSuperAdmin(Admin $admin): bool
    {
        if ($admin->role_id) {
            return (bool) ($admin->rbacRole?->is_super_admin ?? false);
        }
        $p = $admin->permissions;

        return $p === null || (is_array($p) && in_array('*', $p));
    }

    protected function hasLegacyPermission(Admin $admin, string $key): bool
    {
        $p = $admin->permissions;
        if (! is_array($p)) {
            return false;
        }

        return in_array($key, $p) || in_array('*', $p);
    }

    public static function resolvePermissionKey(string $key): string
    {
        return self::$legacyMap[$key] ?? $key;
    }
}
