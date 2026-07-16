<?php

namespace App\Providers;

use App\Services\PaystackService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaystackService::class, function () {
            return new PaystackService(
                secretKey: config('paystack.secret_key'),
                publicKey: config('paystack.public_key'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('redemption', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
