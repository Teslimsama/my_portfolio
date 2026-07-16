<?php

use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

// ── Landing page ──
Route::get('/', function () {
    $products = \App\Models\LicensableProduct::where('is_active', true)->get();
    return view('welcome', ['products' => $products]);
})->name('home');

// ── Public purchase flow ──
Route::get('/purchase/{product:slug}', [PurchaseController::class, 'show'])->name('purchase.show');
Route::post('/purchase/{product:slug}/initiate', [PurchaseController::class, 'initiatePayment'])->name('purchase.initiate');
Route::get('/purchase/{product:slug}/verify', [PurchaseController::class, 'verify'])->name('purchase.verify');
Route::get('/purchase/{product:slug}/success', [PurchaseController::class, 'success'])->name('purchase.success');
