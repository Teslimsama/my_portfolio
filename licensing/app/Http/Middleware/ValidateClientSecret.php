<?php

namespace App\Http\Middleware;

use App\Models\ClientCredential;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ValidateClientSecret
{
    /**
     * Authenticate a client site using X-Client-Key + X-Client-Secret headers.
     *
     * client_key is a public identifier (not hashed, indexed for O(1) lookup).
     * api_secret is bcrypt-hashed — we do a single Hash::check() against one row.
     */
    public function handle(Request $request, Closure $next)
    {
        $clientKey = $request->header('X-Client-Key');
        $clientSecret = $request->header('X-Client-Secret');

        if (! $clientKey || ! $clientSecret) {
            return response()->json([
                'valid' => false,
                'reason' => 'invalid credentials',
            ], 401);
        }

        $credential = ClientCredential::where('client_key', $clientKey)
            ->where('is_active', true)
            ->first();

        if (! $credential) {
            return response()->json([
                'valid' => false,
                'reason' => 'invalid credentials',
            ], 401);
        }

        if (! Hash::check($clientSecret, $credential->api_secret)) {
            return response()->json([
                'valid' => false,
                'reason' => 'invalid credentials',
            ], 401);
        }

        // Inject the authenticated credential into the request for the controller
        $request->merge(['__client_credential' => $credential]);

        return $next($request);
    }
}
