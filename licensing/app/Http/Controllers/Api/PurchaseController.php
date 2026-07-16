<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LicenseCodePurchased;
use App\Models\LicensableProduct;
use App\Services\LicenseCodeGenerator;
use App\Services\PaystackService;
use App\Services\PriceCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly PriceCalculator $priceCalculator,
        private readonly PaystackService $paystackService,
        private readonly LicenseCodeGenerator $codeGenerator,
    ) {
    }

    /**
     * Initiate a Paystack payment.
     *
     * POST /api/purchase/initiate
     *
     * Body: { product_slug, tier_key?, custom_days?, email }
     *
     * The server computes the price — the client never provides it.
     * Computed amount is stored server-side (cache) and also passed to
     * Paystack as metadata so the verify endpoint can cross-check.
     */
    public function initiate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_slug' => 'required|string|exists:licensable_products,slug',
            'tier_key' => 'nullable|string',
            'custom_days' => 'nullable|integer|min:1|max:365',
            'email' => 'required|email|max:255',
        ]);

        if (empty($validated['tier_key']) && empty($validated['custom_days'])) {
            return response()->json([
                'error' => 'Provide either tier_key or custom_days.',
            ], 422);
        }

        $product = LicensableProduct::where('slug', $validated['product_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        // Resolve tier_id from tier_key if provided
        $tierId = null;
        if (! empty($validated['tier_key'])) {
            $tier = $product->priceTiers()
                ->where('tier_key', $validated['tier_key'])
                ->where('is_active', true)
                ->first();

            if (! $tier) {
                return response()->json([
                    'error' => 'Invalid or inactive price tier.',
                ], 422);
            }

            $tierId = $tier->id;
        }

        // Compute price SERVER-SIDE — never trust a client-provided amount
        try {
            $calc = $this->priceCalculator->calculate(
                product: $product,
                tierId: $tierId,
                customDays: $validated['custom_days'] ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }

        $reference = LicenseCodeGenerator::generatePaymentReference();
        $callbackUrl = route('purchase.verify', ['product' => $product->slug]);

        try {
            $response = $this->paystackService->initializeTransaction(
                amount: $calc['amount'],
                email: $validated['email'],
                metadata: [
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                    'duration_days' => $calc['duration_days'],
                    'server_amount' => $calc['amount'], // Cross-check in verify
                    'reference' => $reference,
                ],
                callbackUrl: $callbackUrl,
            );
        } catch (\RuntimeException $e) {
            Log::error('Paystack init failed', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Payment initialization failed. Please try again.',
            ], 502);
        }

        // Store purchase intent server-side so verify can cross-check the amount
        Cache::put("purchase_intent.{$reference}", [
            'product_id' => $product->id,
            'product_slug' => $product->slug,
            'computed_amount' => $calc['amount'],
            'duration_days' => $calc['duration_days'],
            'email' => $validated['email'],
        ], now()->addHours(2));

        return response()->json([
            'authorization_url' => $response['data']['authorization_url'],
            'reference' => $reference,
            'public_key' => $this->paystackService->getPublicKey(),
            'amount' => $calc['amount'],
            'access_code' => $response['data']['access_code'] ?? null,
        ]);
    }

    /**
     * Verify a Paystack payment and issue a license code.
     *
     * GET /api/purchase/verify?reference=XXX
     *
     * Cross-checks the amount Paystack confirms against the server-computed
     * amount stored at initiate time. If they don't match → rejected.
     */
    public function verify(Request $request): JsonResponse
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return response()->json([
                'success' => false,
                'error' => 'No payment reference provided.',
            ], 400);
        }

        // 1. Fetch transaction from Paystack as source of truth
        try {
            $verification = $this->paystackService->verifyTransaction($reference);
        } catch (\RuntimeException $e) {
            Log::error('Paystack verify failed', ['reference' => $reference, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Payment verification failed. Please try again or contact support.',
            ], 502);
        }

        $data = $verification['data'] ?? [];

        if (($data['status'] ?? '') !== 'success') {
            return response()->json([
                'success' => false,
                'error' => 'Payment was not successful.',
            ], 422);
        }

        // 2. Cross-check amount: what Paystack says was paid vs what we computed
        $paystackAmount = round(($data['amount'] ?? 0) / 100, 2); // Convert kobo → Naira

        $intent = Cache::get("purchase_intent.{$reference}");
        $computedAmount = $intent['computed_amount'] ?? null;

        if ($computedAmount === null) {
            // Cache may have expired — fall back to Paystack metadata
            $metadata = $data['metadata'] ?? [];
            $computedAmount = $metadata['server_amount'] ?? null;
        }

        if ($computedAmount !== null && abs($paystackAmount - $computedAmount) > 0.01) {
            Log::warning('Amount mismatch in payment verification', [
                'reference' => $reference,
                'paystack_amount' => $paystackAmount,
                'computed_amount' => $computedAmount,
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Payment amount does not match. Please contact support.',
            ], 422);
        }

        // 3. Idempotency: has a code already been issued for this reference?
        $existingCode = \App\Models\LicenseCode::where('payment_reference', $reference)->first();

        if ($existingCode) {
            // Already issued (webhook or prior verify call)
            $product = $existingCode->licensableProduct;
            return response()->json([
                'success' => true,
                'code' => $existingCode->code,
                'duration_days' => $existingCode->duration_days,
                'product_name' => $product->name ?? '',
            ]);
        }

        // 4. Get product and purchase details
        $productId = $intent['product_id'] ?? ($data['metadata']['product_id'] ?? null);
        $durationDays = $intent['duration_days'] ?? ($data['metadata']['duration_days'] ?? 0);
        $email = $intent['email'] ?? ($data['customer']['email'] ?? '');

        if (! $productId) {
            Log::error('Payment verify: no product_id found', ['reference' => $reference]);
            return response()->json([
                'success' => false,
                'error' => 'Could not determine the product. Please contact support.',
            ], 500);
        }

        $product = LicensableProduct::find($productId);
        if (! $product) {
            return response()->json([
                'success' => false,
                'error' => 'Product not found.',
            ], 500);
        }

        // 5. Generate the license code (unique payment_reference index handles race)
        try {
            $code = DB::transaction(function () use ($product, $reference, $durationDays, $paystackAmount) {
                $generatedCode = $this->codeGenerator->generate($product);

                return \App\Models\LicenseCode::create([
                    'licensable_product_id' => $product->id,
                    'code' => $generatedCode,
                    'duration_days' => $durationDays,
                    'price_paid' => $paystackAmount,
                    'payment_reference' => $reference,
                    'status' => 'unused',
                ]);
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Lost the race — webhook or another verify call already handled this
            $code = \App\Models\LicenseCode::where('payment_reference', $reference)->first();
        }

        if (! $code) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate license code. Please contact support.',
            ], 500);
        }

        // 6. Queue email
        if ($email) {
            Mail::to($email)->queue(new LicenseCodePurchased($code));
        }

        // 7. Clean up cache
        Cache::forget("purchase_intent.{$reference}");

        return response()->json([
            'success' => true,
            'code' => $code->code,
            'duration_days' => $code->duration_days,
            'product_name' => $product->name,
        ]);
    }
}
