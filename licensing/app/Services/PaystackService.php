<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    public function __construct(
        private readonly string $secretKey,
        private readonly string $publicKey,
    ) {
    }

    /**
     * Initialize a Paystack transaction.
     *
     * @param float  $amount      Amount in Naira (will be converted to kobo)
     * @param string $email       Customer email
     * @param array  $metadata    Custom metadata for the transaction
     * @param string $callbackUrl Where Paystack redirects after payment
     * @return array Paystack response body
     */
    public function initializeTransaction(
        float $amount,
        string $email,
        array $metadata,
        string $callbackUrl,
    ): array {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->post($this->url('/transaction/initialize'), [
                'email' => $email,
                'amount' => $this->toKobo($amount),
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

        if (! $response->successful()) {
            Log::error('Paystack initialize failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Failed to initialize payment: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Verify a transaction by reference.
     */
    public function verifyTransaction(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->get($this->url('/transaction/verify/' . urlencode($reference)));

        if (! $response->successful()) {
            Log::error('Paystack verify failed', [
                'reference' => $reference,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Failed to verify payment: ' . $response->json('message', 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Verify a Paystack webhook signature.
     *
     * Uses HMAC-SHA512 with the secret key.
     * This is the REAL security check — IP whitelisting is secondary.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('x-paystack-signature');

        if (! $signature) {
            return false;
        }

        $payload = $request->getContent();
        $computed = hash_hmac('sha512', $payload, $this->secretKey);

        return hash_equals($computed, $signature);
    }

    /**
     * Check if the request IP is in the configured whitelist (defense-in-depth only).
     */
    public function isIpWhitelisted(string $ip): bool
    {
        $whitelist = config('paystack.webhook_ip_whitelist', []);

        if (empty($whitelist)) {
            return true; // No whitelist configured — rely on signature alone
        }

        return in_array($ip, $whitelist, true);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Convert Naira amount to kobo (Paystack uses kobo).
     */
    private function toKobo(float $naira): int
    {
        return (int) round($naira * 100);
    }

    private function url(string $path): string
    {
        return config('paystack.base_url') . $path;
    }
}
