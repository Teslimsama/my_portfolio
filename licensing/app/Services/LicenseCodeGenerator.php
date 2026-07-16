<?php

namespace App\Services;

use App\Models\LicensableProduct;
use RuntimeException;

class LicenseCodeGenerator
{
    // Exclude ambiguous characters: O/0, I/1
    private const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
    private const GROUP_LENGTH = 4;
    private const GROUP_COUNT = 3; // 3 random groups + prefix = 4 groups total (19 chars)
    private const MAX_ATTEMPTS = 5;

    /**
     * Generate a unique license code for a product.
     *
     * Format: {PREFIX}-{4ALNUM}-{4ALNUM}-{4ALNUM}
     * Example: BLKZ-K3M9-XQ2P-4RTN
     *
     * @throws RuntimeException After MAX_ATTEMPTS collisions.
     */
    public function generate(LicensableProduct $product): string
    {
        $prefix = strtoupper($product->code_prefix);

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $groups = [$prefix];

            for ($i = 0; $i < self::GROUP_COUNT; $i++) {
                $groups[] = $this->randomGroup();
            }

            $code = implode('-', $groups);

            // Check uniqueness in the database
            if (! \App\Models\LicenseCode::where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException(
            "Failed to generate unique license code after " . self::MAX_ATTEMPTS . " attempts."
        );
    }

    private function randomGroup(): string
    {
        $group = '';
        $max = strlen(self::CHARS) - 1;

        for ($i = 0; $i < self::GROUP_LENGTH; $i++) {
            $group .= self::CHARS[random_int(0, $max)];
        }

        return $group;
    }

    /**
     * Generate a unique payment reference for Paystack.
     */
    public static function generatePaymentReference(): string
    {
        return 'LIC-' . strtoupper(bin2hex(random_bytes(8)));
    }
}
