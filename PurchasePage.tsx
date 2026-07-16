import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import {
  fetchProduct,
  initiatePurchase,
  ProductResponse,
  PriceTier,
} from "./api";

// Extend Window for Paystack inline JS
declare global {
  interface Window {
    PaystackPop?: {
      setup: (config: PaystackConfig) => { openIframe: () => void };
    };
  }
}

interface PaystackConfig {
  key: string;
  email: string;
  amount: number;
  ref: string;
  metadata: { custom_fields: Array<{ variable_name: string; value: string }> };
  onClose: () => void;
  callback: (response: { reference: string }) => void;
}

const PurchasePage: React.FC = () => {
  const { slug } = useParams<{ slug: string }>();
  const navigate = useNavigate();

  const [product, setProduct] = useState<ProductResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Form state
  const [selectedTierKey, setSelectedTierKey] = useState<string | null>(null);
  const [customDays, setCustomDays] = useState<number>(1);
  const [email, setEmail] = useState("");
  const [paying, setPaying] = useState(false);

  // Load product on mount
  useEffect(() => {
    if (!slug) return;
    setLoading(true);
    fetchProduct(slug)
      .then(setProduct)
      .catch((e) => setError(e.message))
      .finally(() => setLoading(false));
  }, [slug]);

  // Load Paystack inline JS
  useEffect(() => {
    if (document.querySelector('script[src="https://js.paystack.co/v2/inline.js"]'))
      return;
    const script = document.createElement("script");
    script.src = "https://js.paystack.co/v2/inline.js";
    script.async = true;
    document.head.appendChild(script);
  }, []);

  const selectedTier: PriceTier | null =
    product?.tiers.find((t) => t.tier_key === selectedTierKey) ?? null;

  const isCustom = selectedTierKey === "custom";
  const calculatedAmount = isCustom
    ? (product?.daily_rate ?? 0) * customDays
    : (selectedTier?.calculated_price ?? 0);

  const handlePay = async () => {
    if (!product || !email) return;

    setPaying(true);
    setError(null);

    try {
      const result = await initiatePurchase({
        product_slug: product.slug,
        tier_key: isCustom ? undefined : (selectedTierKey ?? undefined),
        custom_days: isCustom ? customDays : undefined,
        email,
      });

      // Open Paystack inline popup
      const handler = window.PaystackPop?.setup({
        key: result.public_key,
        email,
        amount: Math.round(result.amount * 100), // Naira → kobo
        ref: result.reference,
        metadata: {
          custom_fields: [
            { variable_name: "product_slug", value: product.slug },
          ],
        },
        onClose: () => {
          setPaying(false);
        },
        callback: (response: { reference: string }) => {
          // Paystack popup callback — navigate to verify
          navigate(
            `/purchase/${product.slug}/success?reference=${response.reference}`,
          );
        },
      });

      handler?.openIframe();
    } catch (e) {
      setError(e instanceof Error ? e.message : "Payment failed");
      setPaying(false);
    }
  };

  // Loading state
  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center space-y-3">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600 mx-auto" />
          <p className="text-gray-500 text-sm">Loading product...</p>
        </div>
      </div>
    );
  }

  // Error state
  if (error && !product) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div className="bg-white rounded-lg border border-red-200 p-8 max-w-md text-center">
          <p className="text-red-600 font-medium mb-2">Failed to load product</p>
          <p className="text-sm text-gray-500 mb-4">{error}</p>
          <button
            onClick={() => window.location.reload()}
            className="text-sm text-amber-600 hover:text-amber-700 font-medium">
            Try again →
          </button>
        </div>
      </div>
    );
  }

  if (!product) return null;

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        {/* Back link */}
        <a
          href="/"
          className="text-sm text-gray-500 hover:text-gray-700 transition-colors">
          ← Back to portfolio
        </a>

        {/* Product header */}
        <div>
          <h1 className="text-3xl font-bold text-gray-900">{product.name}</h1>
          {product.description && (
            <p className="mt-2 text-gray-600">{product.description}</p>
          )}
          <p className="mt-2 text-sm text-gray-500">
            Base rate:{" "}
            <span className="font-semibold text-gray-900">
              ₦{product.daily_rate.toLocaleString("en-NG", { minimumFractionDigits: 2 })}
            </span>{" "}
            / day
          </p>
        </div>

        {/* Tier cards */}
        <div>
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Select a Plan</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {product.tiers.map((tier) => {
              const custom = tier.tier_key === "custom";
              const selected = selectedTierKey === tier.tier_key;
              return (
                <div
                  key={tier.tier_key}
                  onClick={() => {
                    setSelectedTierKey(tier.tier_key);
                    setError(null);
                  }}
                  className={`relative rounded-lg border-2 p-4 cursor-pointer transition-all ${
                    selected
                      ? "border-amber-500 bg-amber-50 shadow-md"
                      : custom
                        ? "border-dashed border-gray-300 hover:border-amber-400"
                        : "border-gray-200 hover:border-amber-400 hover:shadow-sm"
                  }`}>
                  <div className="text-center">
                    <h3 className="text-lg font-bold text-gray-900">
                      {custom ? "Custom" : `${tier.duration_days} Days`}
                    </h3>
                    {tier.discount_pct > 0 && (
                      <span className="inline-block mt-1 px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                        {tier.discount_pct}% OFF
                      </span>
                    )}
                    <p className="mt-2 text-2xl font-bold text-amber-600">
                      {custom
                        ? `₦${product.daily_rate.toLocaleString("en-NG", { minimumFractionDigits: 2 })}/day`
                        : `₦${(tier.calculated_price ?? 0).toLocaleString("en-NG", { minimumFractionDigits: 2 })}`}
                    </p>
                    {!custom && tier.discount_pct > 0 && (
                      <p className="text-sm text-gray-400 line-through">
                        ₦
                        {(
                          product.daily_rate * (tier.duration_days ?? 0)
                        ).toLocaleString("en-NG", { minimumFractionDigits: 2 })}
                      </p>
                    )}
                    {!custom && (
                      <p className="text-xs text-gray-400 mt-1">
                        ₦{tier.price_per_day.toFixed(2)}/day
                      </p>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Custom days input */}
        {isCustom && (
          <div className="bg-white rounded-lg border border-gray-200 p-4 max-w-xs">
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Number of Days
            </label>
            <input
              type="number"
              min={1}
              max={365}
              value={customDays}
              onChange={(e) => setCustomDays(Math.max(1, parseInt(e.target.value) || 1))}
              className="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm px-3 py-2 border"
            />
            <p className="mt-2 text-sm text-gray-600">
              Total:{" "}
              <span className="font-bold text-amber-600">
                ₦
                {calculatedAmount.toLocaleString("en-NG", {
                  minimumFractionDigits: 2,
                })}
              </span>
            </p>
          </div>
        )}

        {/* Error */}
        {error && (
          <div className="rounded-md bg-red-50 p-4 border border-red-200">
            <p className="text-sm text-red-700">{error}</p>
          </div>
        )}

        {/* Email + Pay button */}
        {selectedTierKey && (
          <div className="space-y-4 max-w-md">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Email Address
              </label>
              <p className="text-xs text-gray-500 mb-1">
                Your license code will be sent here.
              </p>
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="you@example.com"
                className="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm px-3 py-2 border"
              />
            </div>

            <button
              onClick={handlePay}
              disabled={paying || !email}
              className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
              {paying ? (
                <>
                  <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2" />
                  Processing...
                </>
              ) : (
                `Pay ₦${calculatedAmount.toLocaleString("en-NG", { minimumFractionDigits: 2 })} with Paystack`
              )}
            </button>

            <p className="text-xs text-gray-400">
              🔒 Payments are securely processed by Paystack.
            </p>
          </div>
        )}
      </div>
    </div>
  );
};

export default PurchasePage;
