<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiatePaymentRequest;
use App\Mail\LicenseCodePurchased;
use App\Models\LicensableProduct;
use App\Services\LicenseCodeGenerator;
use App\Services\PaystackService;
use App\Services\PriceCalculator;
use Illuminate\Http\Request;
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
     * Show the purchase page for a product.
     */
    public function show(string $slug)
    {
        $product = LicensableProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $tiers = $product->priceTiers()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('purchase.show', [
            'product' => $product,
            'tiers' => $tiers,
            'paystackPublicKey' => $this->paystackService->getPublicKey(),
        ]);
    }

    /**
     * Initiate a Paystack payment. Returns JSON for AJAX or redirects.
     */
    public function initiatePayment(InitiatePaymentRequest $request, string $slug)
    {
        $product = LicensableProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        try {
            $calc = $this->priceCalculator->calculate(
                product: $product,
                tierId: $request->integer('tier_id') ?: null,
                customDays: $request->integer('custom_days') ?: null,
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['payment' => $e->getMessage()]);
        }

        $reference = LicenseCodeGenerator::generatePaymentReference();
        $callbackUrl = route('purchase.verify', ['product' => $product->slug]);

        try {
            $response = $this->paystackService->initializeTransaction(
                amount: $calc['amount'],
                email: $request->input('email'),
                metadata: [
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                    'duration_days' => $calc['duration_days'],
                    'amount' => $calc['amount'],
                    'tier_id' => $calc['tier']?->id,
                    'reference' => $reference,
                ],
                callbackUrl: $callbackUrl,
            );
        } catch (\RuntimeException $e) {
            Log::error('Paystack init failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['payment' => 'Payment initialization failed. Please try again.']);
        }

        // Store purchase intent in session for the callback
        session()->put("purchase.{$reference}", [
            'product_id' => $product->id,
            'duration_days' => $calc['duration_days'],
            'amount' => $calc['amount'],
            'email' => $request->input('email'),
        ]);

        // Redirect to Paystack checkout
        return redirect($response['data']['authorization_url']);
    }

    /**
     * Handle Paystack redirect callback after payment.
     * Both the user's browser AND Paystack's webhook can hit code generation —
     * the unique index on payment_reference prevents double-issuing.
     */
    public function verify(Request $request, string $slug)
    {
        $product = LicensableProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('purchase.show', ['product' => $product->slug])
                ->withErrors(['payment' => 'No payment reference found.']);
        }

        try {
            $verification = $this->paystackService->verifyTransaction($reference);
        } catch (\RuntimeException $e) {
            Log::error('Paystack verify failed', ['reference' => $reference, 'error' => $e->getMessage()]);
            return redirect()->route('purchase.show', ['product' => $product->slug])
                ->withErrors(['payment' => 'Payment verification failed. Please contact support.']);
        }

        $data = $verification['data'] ?? [];

        if (($data['status'] ?? '') !== 'success') {
            return redirect()->route('purchase.show', ['product' => $product->slug])
                ->withErrors(['payment' => 'Payment was not successful. Please try again.']);
        }

        // idempotency: check if a code already exists for this reference
        $existingCode = \App\Models\LicenseCode::where('payment_reference', $reference)->first();

        if ($existingCode) {
            // Already generated (likely by webhook) — just show it
            session()->flash('license_code', $existingCode->code);
            session()->flash('purchase_duration_days', $existingCode->duration_days);
            session()->flash('purchase_product_name', $product->name);

            return redirect()->route('purchase.success', ['product' => $product->slug]);
        }

        // Generate code — wrapped in try/catch for the race condition with webhook
        $durationDays = session("purchase.{$reference}.duration_days", 0);
        $amount = session("purchase.{$reference}.amount", 0);
        $email = session("purchase.{$reference}.email", $data['customer']['email'] ?? '');

        try {
            $code = DB::transaction(function () use ($product, $reference, $durationDays, $amount) {
                $generatedCode = $this->codeGenerator->generate($product);

                return \App\Models\LicenseCode::create([
                    'licensable_product_id' => $product->id,
                    'code' => $generatedCode,
                    'duration_days' => $durationDays,
                    'price_paid' => $amount,
                    'payment_reference' => $reference,
                    'status' => 'unused',
                ]);
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Lost the race — webhook already created the code for this reference
            $code = \App\Models\LicenseCode::where('payment_reference', $reference)->first();
        }

        if (! $code) {
            Log::error('Failed to create or find license code', ['reference' => $reference]);
            return redirect()->route('purchase.show', ['product' => $product->slug])
                ->withErrors(['payment' => 'An error occurred generating your license code. Please contact support.']);
        }

        Mail::to($email)->queue(new LicenseCodePurchased($code));

        session()->flash('license_code', $code->code);
        session()->flash('purchase_duration_days', $code->duration_days);
        session()->flash('purchase_product_name', $product->name);

        // Clean up session
        session()->forget("purchase.{$reference}");

        return redirect()->route('purchase.success', ['product' => $product->slug]);
    }

    /**
     * Show the success page with the license code.
     */
    public function success(string $slug)
    {
        $product = LicensableProduct::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $code = session('license_code');
        $durationDays = session('purchase_duration_days');
        $productName = session('purchase_product_name');

        if (! $code) {
            return redirect()->route('purchase.show', ['product' => $product->slug]);
        }

        return view('purchase.success', [
            'product' => $product,
            'code' => $code,
            'durationDays' => $durationDays,
            'productName' => $productName,
        ]);
    }
}
