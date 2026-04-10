<?php

/**
 * Maps admin route name prefixes to required RBAC permission keys.
 * More specific prefixes are matched first. Admin needs the listed permission to access.
 */
return [
    // === CAMPAIGNS ===
    'admin.campaigns.status.update'    => 'campaigns.approve',
    'admin.campaigns.featured.update' => 'campaigns.update',
    'admin.campaigns.upload-campaign-image' => 'campaigns.update',
    'admin.campaigns.fix-images'      => 'campaigns.update',
    'admin.campaigns.update'          => 'campaigns.update',
    'admin.campaigns.edit'            => 'campaigns.update',
    'admin.campaigns'                 => 'campaigns.view',

    // === CAMPAIGN COMMENTS ===
    'admin.comments.approve'          => 'campaigns.comments_approve',
    'admin.comments.delete'           => 'campaigns.comments_approve',
    'admin.comments'                  => 'campaigns.view',

    // === USERS ===
    'admin.user.kyc.approve'          => 'users.kyc_approve',
    'admin.user.kyc.cancel'           => 'users.kyc_approve',
    'admin.user.push.device.store'    => 'users.update',
    'admin.user.push.device.update'   => 'users.update',
    'admin.user.push.device.delete'   => 'users.update',
    'admin.user.update'               => 'users.update',
    'admin.user.password.change'      => 'users.update',
    'admin.user.add.sub.balance'      => 'users.update',
    'admin.user.status'               => 'users.update',
    'admin.user.send.email'           => 'users.update',
    'admin.user.send.email.post'      => 'users.update',
    'admin.user.send.bulk.email'      => 'users.update',
    'admin.user.send.bulk.email.post' => 'users.update',
    'admin.user.delete.all.users'     => 'users.delete',
    'admin.user.delete.selected'      => 'users.delete',
    'admin.user'                      => 'users.view',

    // === DONATIONS ===
    'admin.donations.approve'         => 'donations.approve',
    'admin.donations.approve.form'    => 'donations.approve',
    'admin.donations.reject'          => 'donations.approve',
    'admin.donations'                 => 'donations.view',

    // === REWARDS (donations module) ===
    'admin.rewards'                   => 'donations.view',

    // === WITHDRAWALS ===
    'admin.withdraw.approve'          => 'withdrawals.approve',
    'admin.withdraw.cancel'           => 'withdrawals.approve',
    'admin.withdraw'                  => 'withdrawals.view',

    // === WITHDRAW METHOD ===
    'admin.withdraw.method.store'     => 'withdrawals.manage',
    'admin.withdraw.method.status'    => 'withdrawals.manage',
    'admin.withdraw.method.edit'      => 'withdrawals.manage',
    'admin.withdraw.method.new'       => 'withdrawals.manage',
    'admin.withdraw.method'           => 'withdrawals.view',

    // === CATEGORIES ===
    'admin.categories.store'          => 'categories.manage',
    'admin.categories.status'         => 'categories.manage',
    'admin.categories'                => 'categories.view',

    // === SUBCATEGORIES ===
    'admin.subcategories.store'       => 'categories.manage',
    'admin.subcategories.status'      => 'categories.manage',
    'admin.subcategories.delete'      => 'categories.manage',
    'admin.subcategories'             => 'categories.view',

    // === HEADER CATEGORIES ===
    'admin.header-categories.store'   => 'categories.manage',
    'admin.header-categories.status'  => 'categories.manage',
    'admin.header-categories.delete' => 'categories.manage',
    'admin.header-categories'         => 'categories.view',

    // === FOOTER CATEGORIES ===
    'admin.footer-categories.store'   => 'categories.manage',
    'admin.footer-categories.status'  => 'categories.manage',
    'admin.footer-categories.delete'  => 'categories.manage',
    'admin.footer-categories'         => 'categories.view',

    // === BANNERS ===
    'admin.banners.create'            => 'banners.manage',
    'admin.banners.store'             => 'banners.manage',
    'admin.banners.edit'              => 'banners.manage',
    'admin.banners.update'            => 'banners.manage',
    'admin.banners.destroy'           => 'banners.manage',
    'admin.banners'                   => 'banners.view',

    // === BLOG / DSA POSTS ===
    'admin.blog.create'               => 'campaigns.create',
    'admin.blog.store'                => 'campaigns.create',
    'admin.blog.edit'                 => 'campaigns.update',
    'admin.blog.update'               => 'campaigns.update',
    'admin.blog.destroy'              => 'campaigns.delete',
    'admin.blog'                      => 'campaigns.view',

    // === GATEWAYS ===
    'admin.gateway.automated.update'  => 'gateways.manage',
    'admin.gateway.automated.remove'  => 'gateways.manage',
    'admin.gateway.automated.status'  => 'gateways.manage',
    'admin.gateway.automated.edit'    => 'gateways.manage',
    'admin.gateway.manual.store'      => 'gateways.manage',
    'admin.gateway.manual.status'     => 'gateways.manage',
    'admin.gateway.manual.edit'       => 'gateways.manage',
    'admin.gateway.manual.new'        => 'gateways.manage',
    'admin.gateway'                   => 'gateways.view',

    // === CURRENCIES (FX rates) ===
    'admin.currencies.sync'           => 'gateways.manage',
    'admin.currencies.store'        => 'gateways.manage',
    'admin.currencies.update'       => 'gateways.manage',
    'admin.currencies'              => 'gateways.view',

    // === PAYOUT BANKS ===
    'admin.payout-banks.store'        => 'payout_banks.manage',
    'admin.payout-banks.status'       => 'payout_banks.manage',
    'admin.payout-banks.delete'       => 'payout_banks.manage',
    'admin.payout-banks'              => 'payout_banks.view',

    // === CREATOR PAYOUTS ===
    'admin.creator-payouts.partial'   => 'creator_payouts.manage',
    'admin.creator-payouts.full'      => 'creator_payouts.manage',
    'admin.creator-payouts.fulfillment' => 'creator_payouts.manage',
    'admin.creator-payouts'           => 'creator_payouts.view',

    // === CREATOR PAYOUT SETTINGS ===
    'admin.creator-payout.settings.update' => 'creator_payouts.manage',
    'admin.creator-payout.settings.edit' => 'creator_payouts.manage',
    'admin.creator-payout.settings'   => 'creator_payouts.view',

    // === TRANSACTIONS / STORE ===
    'admin.transaction'               => 'transactions.view',
    'admin.store'                     => 'transactions.view',

    // === CONTACTS ===
    'admin.contact.remove'            => 'contacts.manage',
    'admin.contact.status'            => 'contacts.manage',
    'admin.contact'                   => 'contacts.view',

    // === SUBSCRIBERS ===
    'admin.subscriber.remove'         => 'subscribers.manage',
    'admin.subscriber.send'           => 'subscribers.manage',
    'admin.subscriber'                => 'subscribers.view',

    // === SETTINGS - Per-item permissions ===
    'admin.basic.system.setting'      => 'settings.basic',
    'admin.basic.logo.favicon.setting' => 'settings.basic',
    'admin.basic.cover.setting'       => 'settings.basic',
    'admin.basic'                     => 'settings.basic',
    'admin.home.setting'              => 'settings.home',
    'admin.home'                      => 'settings.home',
    'admin.social.login'              => 'settings.social_login',
    'admin.firebase.otp'              => 'settings.firebase_otp',
    'admin.fcm.push'                  => 'settings.firebase_otp',
    'admin.gemini'                    => 'settings.gemini',
    'admin.plugin.setting'            => 'settings.plugins',
    'admin.plugin.status'             => 'settings.plugins',
    'admin.language.store'           => 'settings.language',
    'admin.language.status'           => 'settings.language',
    'admin.language.delete'          => 'settings.language',
    'admin.language.import'           => 'settings.language',
    'admin.language'                  => 'settings.language',
    'admin.seo.setting'               => 'settings.seo',
    'admin.kyc.setting'               => 'settings.kyc',
    'admin.homepage'                  => 'settings.site_content',
    'admin.site.sections'             => 'settings.site_content',
    'admin.site.themes'               => 'settings.themes',
    'admin.customcode'                => ['settings.customcode', 'settings.site_content'],
    'admin.report.fundraiser'         => ['settings.report', 'settings.site_content'],
    'admin.report'                   => ['settings.report', 'settings.site_content'],
    'admin.site'                     => 'settings.site_content',
    'admin.cookie.setting'            => 'settings.cookie',
    'admin.cookie'                    => 'settings.cookie',
    'admin.maintenance.setting'       => 'settings.maintenance',
    'admin.maintenance'               => 'settings.maintenance',
    'admin.cache.clear'               => 'settings.cache',
    'admin.cache'                     => 'settings.cache',

    // === NOTIFICATIONS ===
    'admin.notification.template'     => 'notifications.manage',
    'admin.notification.email'       => 'notifications.manage',
    'admin.notification.sms'          => 'notifications.manage',
    'admin.notification'              => 'notifications.view',
    'admin.email-logs'                => 'notifications.view',

    // === UPLOAD ===
    'admin.upload'                    => 'campaigns.view',

    // === ACTIVITY LOGS ===
    'admin.activity-logs'             => 'activity_logs.view',

    // admin-users and admin.roles are handled separately in middleware (super admin only)
];
