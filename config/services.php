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

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT', env('APP_PROTOCOL', 'https').'://'.env('APP_URL', 'passport.showheroes.com').'/auth/google/callback'),
    ],

    'default_team_ui_colors' => [
        'header_bg_color' => '#00c3dd',
        'primary_light_color' => '#00c3dd',
        'hamburger_bg_color' => '#fc34be',
        'hamburger_bg_dark_color' => '#385c66',
        'menu_highlight_color' => '#feff78',
        'menu_highlight_dark_color' => '#00c4e0',
    ],

    'allow_domains' => [
        'showheroes.com',
        'showheroes-group.com'
    ]
];
