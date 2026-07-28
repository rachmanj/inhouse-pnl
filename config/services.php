<?php

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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'hermes' => [
        'base_url' => env('HERMES_BASE_URL', 'http://localhost:3000'),
        'token' => env('HERMES_API_TOKEN'),
        'webhook_secret' => env('HERMES_WEBHOOK_SECRET'),
        'default_email_recipient' => env('HERMES_DEFAULT_EMAIL_RECIPIENT'),
        'default_whatsapp_recipient' => env('HERMES_DEFAULT_WHATSAPP_RECIPIENT'),
        'default_telegram_chat_id' => env('HERMES_DEFAULT_TELEGRAM_CHAT_ID'),
    ],

];
