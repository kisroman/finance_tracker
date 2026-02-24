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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'currency_rates' => [
        'base_url' => env('CURRENCY_RATE_API_BASE', 'https://open.er-api.com/v6/latest'),
        'api_key' => env('CURRENCY_RATE_API_KEY'),
        'api_key_location' => env('CURRENCY_RATE_API_KEY_LOCATION', 'query'),
        'api_key_name' => env('CURRENCY_RATE_API_KEY_NAME', 'access_key'),
        'api_key_header_prefix' => env('CURRENCY_RATE_API_KEY_HEADER_PREFIX', ''),
        'base_currency' => env('CURRENCY_BASE', 'UAH'),
        'cache_seconds' => env('CURRENCY_RATE_CACHE_SECONDS', 3600),
    ],

];
