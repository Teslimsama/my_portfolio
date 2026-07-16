<?php

namespace App\Services;

use App\Models\LicensableProduct;
use App\Models\PriceTier;
use InvalidArgumentException;

class PriceCalculator
{
    /**
     * Calculate the final price for a license purchase.
     *
     * @param LicensableProduct $product    The product being purchased
     * @param int|null          $tierId     Price tier ID (for fixed tiers)
     * @param int|null          $customDays Custom duration in days (for custom tier)
     * @return array{duration_days: int, amount: float, discount_pct: float, original_amount: float, tier: ?PriceTier}
     *
     * @throws InvalidArgumentException
     */
    public function calculate(
        LicensableProduct $product,
        ?int $tierId = null,
        ?int $customDays = null,
    ): array {
        // Must provide either tier_id or custom_days (not both, not neither)
        if ($tierId && $customDays) {
            throw new InvalidArgumentException('Provide either a tier or custom days, not both.');
        }

        if ($tierId) {
            return $this->calculateFromTier($product, $tierId);
        }

        if ($customDays) {
            return $this->calculateCustom($product, $customDays);
        }

        throw new InvalidArgumentException('Provide either a tier or custom days.');
    }

    private function calculateFromTier(LicensableProduct $product, int $tierId): array
    {
        $tier = PriceTier::where('id', $tierId)
            ->where('licensable_product_id', $product->id)
            ->where('is_active', true)
            ->first();

        if (! $tier) {
            throw new InvalidArgumentException('Invalid or inactive price tier.');
        }

        if ($tier->tier_key === 'custom') {
            throw new InvalidArgumentException(
                'Custom tier requires a custom_days value. Use the custom days input.'
            );
        }

        $durationDays = $tier->duration_days;
        $originalAmount = round($product->daily_rate * $durationDays, 2);
        $discountPct = (float) $tier->discount_pct;
        $amount = round($originalAmount * (1 - $discountPct / 100), 2);

        return [
            'duration_days' => $durationDays,
            'amount' => $amount,
            'discount_pct' => $discountPct,
            'original_amount' => $originalAmount,
            'tier' => $tier,
        ];
    }

    private function calculateCustom(LicensableProduct $product, int $customDays): array
    {
        if ($customDays < 1) {
            throw new InvalidArgumentException('Custom days must be at least 1.');
        }

        $amount = round($product->daily_rate * $customDays, 2);

        return [
            'duration_days' => $customDays,
            'amount' => $amount,
            'discount_pct' => 0.0,
            'original_amount' => $amount,
            'tier' => null,
        ];
    }
}
