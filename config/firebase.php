<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase services including Authentication and Firestore.
    | Make sure to set these values in your .env file.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),
    
    'service_account' => [
        'type' => 'service_account',
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
        'private_key' => env('FIREBASE_PRIVATE_KEY'),
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => env('FIREBASE_CLIENT_CERT_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Auth Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to Firebase Authentication.
    |
    */

    'auth' => [
        'verify_phone_number' => env('FIREBASE_VERIFY_PHONE_NUMBER', true),
        'phone_auth_timeout' => env('FIREBASE_PHONE_AUTH_TIMEOUT', 60), // seconds
        'max_otp_attempts' => env('FIREBASE_MAX_OTP_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Firestore Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firestore database operations.
    |
    */

    'firestore' => [
        'database_id' => env('FIREBASE_DATABASE_ID', '(default)'),
        'collection_prefix' => env('FIREBASE_COLLECTION_PREFIX', 'apnafund'),
    ],
];
