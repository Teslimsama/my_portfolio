<?php

use Illuminate\Support\Facades\Route;

// ── Landing page ──
Route::get('/', function () {
    $products = \App\Models\LicensableProduct::where('is_active', true)->get();
    return view('welcome', ['products' => $products]);
})->name('home');
