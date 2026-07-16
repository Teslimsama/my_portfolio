<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LicenseCodePurchased;
use App\Services\LicenseCodeGenerator;
use App\Services\PaystackService;
use App\Models\LicenseCode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private readonly PaystackService $paystackService,
        private readonly LicenseCodeGenerator $codeGenerator,
    ) {
    }

    /**
     * Handle incoming Paystack webhook.
     *
     * Validates the HMAC-SHA512 signature first (the real security check),
     * then optionally checks IP whitelist (defense-in-depth only).
     *
     * Idempotent: if a code already exists for this payment_reference,
     * returns 200 without creating a duplicate.
     */
    public function handlePaystack(Request $request): Response
    {
        // 1. Verify signature (primary security check)
        if (! $this->paystackService->verifyWebhookSignature($request)) {
            Log::warning('Paystack webhook: invalid signature', [
                'ip' => $request->ip(),
            ]);
            return response('Invalid signature', 401);
        }

        // 2. Optional IP whitelist check (secondary)
        $whitelist = config('paystack.webhook_ip_whitelist', []);
        if (! empty($whitelist) && ! $this->paystackService->isIpWhitelisted($request->ip())) {
            Log::warning('Paystack webhook: IP not whitelisted', [
                'ip' => $request->ip(),
            ]);
            // Do NOT block — signature already verified. Just log.
        }

        $payload = $request->all();

        // 3. Only process successful charge events
        if (($payload['event'] ?? '') !== 'charge.success') {
            return response('Event ignored', 200);
        }

        $data = $payload['data'] ?? [];
        $reference = $data['reference'] ?? null;

        if (! $reference) {
            return response('Missing reference', 400);
        }

        // 4. Idempotency check — has this payment already been processed?
        $existingCode = LicenseCode::where('payment_reference', $reference)->first();
        if ($existingCode) {
            return response('Already processed', 200);
        }

        // 5. Extract metadata
        $metadata = $data['metadata'] ?? [];
        $productId = $metadata['product_id'] ?? null;
        $durationDays = $metadata['duration_days'] ?? 0;
        $amount = $metadata['amount'] ?? ($data['amount'] ?? 0) / 100; // Convert from kobo

        if (! $productId || ! $durationDays) {
            Log::error('Paystack webhook: missing metadata', ['reference' => $reference, 'metadata' => $metadata]);
            return response('Missing metadata', 400);
        }

        $product = \App\Models\LicensableProduct::find($productId);
        if (! $product) {
            Log::error('Paystack webhook: product not found', ['product_id' => $productId]);
            return response('Product not found', 400);
        }

        // 6. Generate the license code (idempotent via unique index on payment_reference)
        try {
            $code = DB::transaction(function () use ($product, $reference, $durationDays, $amount) {
                $generatedCode = $this->codeGenerator->generate($product);

                return LicenseCode::create([
                    'licensable_product_id' => $product->id,
                    'code' => $generatedCode,
                    'duration_days' => $durationDays,
                    'price_paid' => $amount,
                    'payment_reference' => $reference,
                    'status' => 'unused',
                ]);
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Lost the race — the browser callback already handled this reference
            Log::info('Paystack webhook: code already generated for reference', ['reference' => $reference]);
            return response('Already processed', 200);
        }

        // 7. Queue email to customer
        $customerEmail = $data['customer']['email'] ?? null;
        if ($customerEmail) {
            Mail::to($customerEmail)->queue(new LicenseCodePurchased($code));
        }

        Log::info('Paystack webhook: license code created', [
            'reference' => $reference,
            'code' => $code->code,
            'product' => $product->slug,
        ]);

        return response('OK', 200);
    }
}
