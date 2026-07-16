<?php

use App\Http\Controllers\Api\RedemptionController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// ── Redemption endpoint (client sites call this to validate codes) ──
Route::post('/redeem-code', [RedemptionController::class, 'redeem'])
    ->middleware(['throttle:redemption', 'client.auth']);

// ── Paystack webhook (server-to-server, no auth needed — signature verified) ──
Route::post('/webhook/paystack', [WebhookController::class, 'handlePaystack']);
