<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RedeemCodeRequest;
use App\Models\ClientCredential;
use App\Models\LicenseCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedemptionController extends Controller
{
    /**
     * Redeem a license code for a client site.
     *
     * Atomic transaction with pessimistic row lock prevents double-redemption
     * even under concurrent requests for the same code.
     */
    public function redeem(RedeemCodeRequest $request): JsonResponse
    {
        /** @var ClientCredential $credential */
        $credential = $request->get('__client_credential');

        $code = strtoupper(trim($request->input('code')));
        $domain = $request->input('domain');

        $licenseCode = DB::transaction(function () use ($code, $credential, $domain) {
            $license = LicenseCode::where('code', $code)
                ->where('licensable_product_id', $credential->licensable_product_id)
                ->where('status', 'unused')
                ->lockForUpdate()
                ->first();

            if (! $license) {
                return null;
            }

            $license->update([
                'status' => 'redeemed',
                'redeemed_by_domain' => $domain,
                'redeemed_by_client_key' => $credential->client_name,
                'redeemed_at' => now(),
            ]);

            return $license;
        });

        if (! $licenseCode) {
            return response()->json([
                'valid' => false,
                'reason' => 'License code not found, already redeemed, or not valid for this product.',
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'duration_days' => $licenseCode->duration_days,
        ]);
    }
}
