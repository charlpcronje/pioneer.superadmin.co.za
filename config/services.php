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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'twilio' => [
        'account_sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'chat_service' => env('TWILIO_CHAT_SERVICE'),
        'push_service' => env('TWILIO_PUSH_SERVICE'),
        'phone_number' => env('TWILIO_NUMBER'),
        'whatsapp_number' => env('TWILIO_WHATSAPP_NUMBER'),
        'farmer_push_service' => env('TWILIO_FARMER_PUSH_SERVICE'),
    ],
    'peat' => [
        'api_key' => env('PEAT_API_KEY'),
        'endpoint' => env('PEAT_SERVICE_ENDPOINT')
    ],
    'firebase' => [
        'endpoint' => env('FIREBASE_SERVICE_ENDPOINT'),
        'server_key' => env('FIREBASE_SERVICE_KEY')
            
    ] 

];
