<?php

/**
 * Admin permission keys and their allowed route patterns.
 * null or ['*'] = super admin (full access).
 * Sub admin gets only routes matching their assigned permissions.
 */
return [
    'seo' => ['admin.seo*'],
    'blog' => ['admin.blog*', 'admin.upload.file', 'admin.upload.external-image'],
    'categories' => ['admin.categories*', 'admin.subcategories*', 'admin.header-categories*', 'admin.footer-categories*'],
    'banners' => ['admin.banners*'],
    'campaigns' => ['admin.campaigns*', 'admin.comments*'],
    'users' => ['admin.user*'],
    'gateways' => ['admin.gateway*'],
    'donations' => ['admin.donations*', 'admin.rewards*'],
    'withdrawals' => ['admin.withdraw*'],
    'payout_banks' => ['admin.payout-banks*'],
    'creator_payouts' => ['admin.creator-payouts*'],
    'transactions' => ['admin.transaction*'],
    'store' => ['admin.store*'],
    'contacts' => ['admin.contact*'],
    'subscribers' => ['admin.subscriber*'],
    'basic_settings' => ['admin.basic*', 'admin.home*', 'admin.cache*', 'admin.gemini*', 'admin.firebase*', 'admin.social.login*'],
    'site_settings' => ['admin.site*', 'admin.homepage*', 'admin.customcode*', 'admin.report*'],
    'notifications' => ['admin.notification*', 'admin.email-logs*'],
    'plugins' => ['admin.plugin*'],
    'language' => ['admin.language*'],
    'kyc' => ['admin.kyc*'],
    'themes' => ['admin.site.themes*'],
    'cookie' => ['admin.cookie*'],
    'maintenance' => ['admin.maintenance*'],
];
