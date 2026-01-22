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
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/google-auth/callback',
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
        'sheet_id' => env('GOOGLE_SHEET_ID', null),
        'credentials_path' => env('GOOGLE_SHEETS_CREDENTIALS_PATH', storage_path('app/secrets/google-credentials.json')),
    ],

    'google_analytics' => [
        'id' => env('GOOGLE_ANALYTICS_ID', null),
    ],

    'facebook' => [
        'pixel_id' => env('FACEBOOK_PIXEL_ID', '1128486299305330'),
        'access_token' => env('FACEBOOK_ACCESS_TOKEN', 'EAAJdLdgHTeABOZCVpQvtvUEpeTq9waE3pBoGhRayrO6xhZBEEZCVA8K8ZBj3Ov9ZCnuwiZClZB0hokIZB78BnBJ1eGmzZBPhnqGlsWEyfQwCZACcr2z8Q8g8rSWcZCXzrAllsHY1ZCHzKMCeeLogyArZAzWMynYIXxQRpMA03T0lgAnZB8OLQ62owSwGmxxmwMKNMsZBQdFiQZDZD'),
    ],

    'companycam' => [
        'showcase_id' => env('COMPANYCAM_SHOWCASE_ID', '01057770-8ca0-47a5-a1dc-40128a20f85b'),
    ],

    'facebook_lead' => [
        'api_key' => env('FACEBOOK_LEAD_API_KEY', 'v3KQ7bHcP8fLTjGxE5mRnZ2sAyXu6pDwY9NVtJW4qrMzF'),
    ],

    'retellai' => [
        'api_key' => env('RETELL_AI_API_KEY'),
    ],

];
