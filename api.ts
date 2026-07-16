/**
 * API configuration for the TBO Digital Solutions licensing backend.
 *
 * In development, the Laravel app runs on a separate port (e.g., licensing.test
 * or localhost:8000). In production, it runs at api.teslim.unibooks.com.ng.
 */

const API_BASE =
  import.meta.env.VITE_API_BASE ?? "https://api.teslim.unibooks.com.ng";

export interface ProductResponse {
  name: string;
  slug: string;
  description: string | null;
  daily_rate: number;
  tiers: PriceTier[];
}

export interface PriceTier {
  id: number;
  tier_key: string;
  duration_days: number | null;
  discount_pct: number;
  calculated_price: number | null;
  price_per_day: number;
}

export interface InitiateResponse {
  authorization_url: string;
  reference: string;
  public_key: string;
  amount: number;
  access_code?: string;
}

export interface VerifyResponse {
  success: boolean;
  code?: string;
  duration_days?: number;
  product_name?: string;
  error?: string;
}

export async function fetchProduct(slug: string): Promise<ProductResponse> {
  const res = await fetch(`${API_BASE}/api/products/${encodeURIComponent(slug)}`);
  if (!res.ok) {
    throw new Error(`Failed to load product: ${res.status}`);
  }
  return res.json();
}

export async function initiatePurchase(data: {
  product_slug: string;
  tier_key?: string;
  custom_days?: number;
  email: string;
}): Promise<InitiateResponse> {
  const res = await fetch(`${API_BASE}/api/purchase/initiate`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error((err as { error?: string }).error ?? "Payment initiation failed");
  }
  return res.json();
}

export async function verifyPurchase(
  reference: string,
): Promise<VerifyResponse> {
  const res = await fetch(
    `${API_BASE}/api/purchase/verify?reference=${encodeURIComponent(reference)}`,
  );
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error((err as { error?: string }).error ?? "Payment verification failed");
  }
  return res.json();
}
