<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\RedemptionController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// ── Public: product info (called by React purchase page) ──
Route::get('/products/{slug}', [ProductController::class, 'show']);

// ── Public: purchase flow (called by React purchase page) ──
Route::post('/purchase/initiate', [PurchaseController::class, 'initiate'])
    ->middleware('throttle:10,1');
Route::get('/purchase/verify', [PurchaseController::class, 'verify']);

// ── Redemption endpoint (client sites call this to validate codes) ──
Route::post('/redeem-code', [RedemptionController::class, 'redeem'])
    ->middleware(['throttle:redemption', 'client.auth']);

// ── Paystack webhook (server-to-server, no auth needed — signature verified) ──
Route::post('/webhook/paystack', [WebhookController::class, 'handlePaystack']);
