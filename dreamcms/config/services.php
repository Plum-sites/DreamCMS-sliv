<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'vkontakte' => [
        'client_id' => env('VKAUTH_ID'),
        'client_secret' => env('VKAUTH_SECRET'),
        'redirect' => env('APP_URL') . '/auth/vk/placeholder',
    ],

    'discord' => [
        'client_id' => env('DISCORD_KEY'),
        'client_secret' => env('DISCORD_SECRET'),
        'redirect' => env('APP_URL') . '/auth/discord/placeholder'
    ],

    'steam' => [
        'client_id' => null,
        'client_secret' => env('STEAM_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/auth/steam/placeholder'
    ],

    'telegram' => [
        'bot' => env('TELEGRAM_BOT_NAME'),
        'client_id' => null,
        'client_secret' => env('TELEGRAM_TOKEN'),
        'redirect' => env('APP_URL') . '/auth/telegram/placeholder',
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/auth/google/placeholder'
    ],

    'yandex' => [
        'client_id' => env('YANDEX_CLIENT_ID'),
        'client_secret' => env('YANDEX_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/auth/yandex/placeholder'
    ],

    'mailru' => [
        'client_id' => env('MAILRU_CLIENT_ID'),
        'client_secret' => env('MAILRU_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/auth/mailru/placeholder'
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN', 'YOUR BOT TOKEN HERE')
    ]
];
