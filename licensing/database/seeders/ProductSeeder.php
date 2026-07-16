<?php

namespace Database\Seeders;

use App\Models\LicensableProduct;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the initial product and its price tiers.
     * Idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        $product = LicensableProduct::updateOrCreate(
            ['slug' => 'bolakaz-stocktaking'],
            [
                'name' => 'Bolakaz Stock-Taking',
                'code_prefix' => 'BLKZ',
                'description' => 'Stock-taking and inventory management pass for Bolakaz-powered shop websites.',
                'daily_rate' => 2000.00,
                'is_active' => true,
            ],
        );

        $tiers = [
            [
                'tier_key' => 'fixed_3day',
                'duration_days' => 3,
                'discount_pct' => 5.00,
                'sort_order' => 1,
            ],
            [
                'tier_key' => 'fixed_7day',
                'duration_days' => 7,
                'discount_pct' => 10.00,
                'sort_order' => 2,
            ],
            [
                'tier_key' => 'fixed_30day',
                'duration_days' => 30,
                'discount_pct' => 20.00,
                'sort_order' => 3,
            ],
            [
                'tier_key' => 'custom',
                'duration_days' => null,
                'discount_pct' => 0.00,
                'sort_order' => 4,
            ],
        ];

        foreach ($tiers as $tier) {
            $product->priceTiers()->updateOrCreate(
                ['tier_key' => $tier['tier_key']],
                array_merge($tier, ['is_active' => true]),
            );
        }
    }
}
