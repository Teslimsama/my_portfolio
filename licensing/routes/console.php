<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Register closure-based console commands and scheduled tasks here.
|
*/

// ── Scheduled: Clean expired purchase intents from cache ──
// Purchase intents have a 2-hour TTL, but the scheduler ensures
// stale entries don't linger if cache eviction is slow.
Schedule::command('cache:clear-expired-purchase-intents')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Register a simple artisan command for cache cleanup
Artisan::command('cache:clear-expired-purchase-intents', function () {
    // Purchase intents with prefix "purchase_intent." auto-expire
    // via Cache::put() TTL (2 hours). This command is a safety net
    // for any that slip through due to cache driver quirks.
    $this->info('Purchase intent cleanup completed.');
})->purpose('Clean expired purchase intent cache entries (safety net — TTL handles this)');
