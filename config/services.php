<?php

// Empty .env value (WORDPRESS_POSTS_API_URL=) overrides env(..., 'default') in Laravel — fall back to built-in URLs.
$wpPostsApiUrl = trim((string) env('WORDPRESS_POSTS_API_URL', ''));
$wpBlogHomeUrl = trim((string) env('WORDPRESS_BLOG_HOME_URL', ''));
if ($wpPostsApiUrl === '') {
    $wpPostsApiUrl = 'https://apnacrowdfunding.com/blog/wp-json/custom/posts';
}
if ($wpBlogHomeUrl === '') {
    $wpBlogHomeUrl = 'https://apnacrowdfunding.com/blog';
}

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/user/auth/google/callback'),
    ],

    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'redirect_uri' => env('YOUTUBE_REDIRECT_URI', env('APP_URL') . '/youtube/callback'),
        'credentials_path' => env('YOUTUBE_CREDENTIALS_PATH', storage_path('app/youtube-credentials.json')),
        'access_token' => env('YOUTUBE_ACCESS_TOKEN'),
        'refresh_token' => env('YOUTUBE_REFRESH_TOKEN'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/user/auth/facebook/callback'),
    ],

    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_REDIRECT_URI', env('APP_URL') . '/user/auth/linkedin/callback'),
    ],

    /*
    | WordPress (business-resources success stories). Install wp-plugin-apnafund-crowdfunding-posts on WP.
    | WORDPRESS_POSTS_API_URL = full base e.g. https://yourblog.com/wp-json/custom/posts (no query string)
    | WORDPRESS_BLOG_HOME_URL = public blog home for "View All" link (no trailing slash required)
    */
    'wordpress' => [
        'posts_api_url' => rtrim($wpPostsApiUrl, '/'),
        'blog_home_url' => rtrim($wpBlogHomeUrl, '/'),
    ],

];
