<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paystack Keys
    |--------------------------------------------------------------------------
    */
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Paystack API Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => 'https://api.paystack.co',

    /*
    |--------------------------------------------------------------------------
    | Webhook IP Whitelist (defense-in-depth only)
    |--------------------------------------------------------------------------
    | The HMAC-SHA512 signature verification is the REAL security check.
    | These IPs are a secondary layer and may change — verify against
    | Paystack's current documentation before deploying to production.
    | https://paystack.com/docs/paystack-api/ip-whitelist/
    */
    'webhook_ip_whitelist' => [
        // '52.31.139.75',
        // '52.49.173.169',
        // '52.214.14.220',
    ],
];
