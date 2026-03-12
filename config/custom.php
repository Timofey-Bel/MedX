<?php

return [
    'items_per_page' => env('ITEMS_PER_PAGE', 48),
    'smtp' => [
        'host' => env('SMTP_HOST'),
        'username' => env('SMTP_USERNAME'),
        'password' => env('SMTP_PASSWORD'),
        'from_email' => env('SMTP_FROM_EMAIL'),
        'from_name' => env('SMTP_FROM_NAME'),
        'port' => env('SMTP_PORT', 465),
        'ssl' => env('SMTP_SSL', true),
    ],
    'dadata_token' => env('DADATA_TOKEN'),
    'smsru_token' => env('SMSRU_TOKEN'),
    'zvonobot_api_key' => env('ZVONOBOT_API_KEY'),
    'release_env' => env('APP_RELEASE', 'DEV'),
    'telegram' => [
        'token' => env('TELEGRAM_TOKEN'),
        'chat_id' => env('TELEGRAM_CHATID'),
    ],
    'min_sum' => env('MIN_SUM', 1500),
    'comet_server' => [
        'host' => env('COMET_SERVER_HOST'),
        'port' => env('COMET_SERVER_PORT'),
        'dev_id' => env('COMET_DEV_ID'),
        'dev_key' => env('COMET_DEV_KEY'),
    ],
    'total_cost' => env('TOTAL_COST', 5000),
    'item_cost' => env('ITEM_COST', 500),
    'bonus_ratio' => env('BONUS_RATIO', 0.3),
];
