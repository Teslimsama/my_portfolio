<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LicensableProduct;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get a product with its active price tiers.
     *
     * GET /api/products/{slug}
     *
     * Fully data-driven — any future product added via Filament admin
     * works through this endpoint with zero code changes.
     */
    public function show(string $slug): JsonResponse
    {
        $product = LicensableProduct::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            return response()->json([
                'error' => 'Product not found.',
            ], 404);
        }

        $tiers = $product->priceTiers()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($tier) use ($product) {
                $isCustom = $tier->tier_key === 'custom';
                $days = $tier->duration_days;
                $dailyRate = (float) $product->daily_rate;

                if ($isCustom) {
                    $calculatedPrice = null; // Depends on user input
                    $pricePerDay = $dailyRate;
                } else {
                    $original = $dailyRate * $days;
                    $calculatedPrice = round($original * (1 - (float) $tier->discount_pct / 100), 2);
                    $pricePerDay = round($calculatedPrice / $days, 2);
                }

                return [
                    'id' => $tier->id,
                    'tier_key' => $tier->tier_key,
                    'duration_days' => $tier->duration_days,
                    'discount_pct' => (float) $tier->discount_pct,
                    'calculated_price' => $calculatedPrice,
                    'price_per_day' => $pricePerDay,
                ];
            });

        return response()->json([
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'daily_rate' => (float) $product->daily_rate,
            'tiers' => $tiers,
        ]);
    }
}
