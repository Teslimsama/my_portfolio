<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | The purchase API endpoints are called by the React portfolio on Vercel
    | (a browser). /api/redeem-code and /api/webhook/paystack are server-to-
    | server calls — CORS headers on those responses are harmless and ignored.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],

    'allowed_origins' => [env('CORS_ALLOWED_ORIGIN', 'http://localhost:3000')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Accept', 'Authorization'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
