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
        'collection_prefix' => env('FIREBASE_COLLECTION_PREFIX', 'apnacrowdfunding'),

        /*
         * Chat field mappings - for app/web sync.
         * If mobile app uses different field names, override here.
         * Conversation doc: participants, participant_names, participant_images, last_message, last_message_at, last_sender_id, read_by, campaign_id, campaign_title
         * Message doc: sender_id, sender_name, sender_image, text, created_at
         */
        'chat' => [
            'messages_subcollection' => env('FIREBASE_CHAT_MESSAGES_COLLECTION', 'messages'), // app may use 'chats' or 'messages'
            'participants_field' => env('FIREBASE_CHAT_PARTICIPANTS_FIELD', 'participants'), // app may use 'members' or 'userIds'
            'last_message_field' => env('FIREBASE_CHAT_LAST_MESSAGE_FIELD', 'last_message'),
            'last_message_at_field' => env('FIREBASE_CHAT_LAST_MESSAGE_AT_FIELD', 'last_message_at'),
            'last_sender_id_field' => env('FIREBASE_CHAT_LAST_SENDER_ID_FIELD', 'last_sender_id'),
            'message_text_field' => env('FIREBASE_CHAT_MESSAGE_TEXT_FIELD', 'text'), // app may use 'message', 'content', 'body'
            'message_sender_id_field' => env('FIREBASE_CHAT_MESSAGE_SENDER_ID_FIELD', 'sender_id'), // app may use 'userId'
            'message_created_at_field' => env('FIREBASE_CHAT_MESSAGE_CREATED_AT_FIELD', 'created_at'), // app may use 'timestamp'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Web/Client SDK (for browser chat)
    |--------------------------------------------------------------------------
    | Public config for Firebase JS SDK - safe to expose to frontend.
    */
    'client' => [
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
    ],
];
