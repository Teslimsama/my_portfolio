import React, { useEffect, useState } from "react";
import { useParams, useSearchParams, Link } from "react-router-dom";
import { verifyPurchase, VerifyResponse } from "./api";

const PurchaseSuccess: React.FC = () => {
  const { slug } = useParams<{ slug: string }>();
  const [searchParams] = useSearchParams();
  const reference = searchParams.get("reference");

  const [state, setState] = useState<"loading" | "success" | "error">("loading");
  const [result, setResult] = useState<VerifyResponse | null>(null);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    if (!reference) {
      setState("error");
      setErrorMsg("No payment reference found.");
      return;
    }

    verifyPurchase(reference)
      .then((data) => {
        if (data.success && data.code) {
          setResult(data);
          setState("success");
        } else {
          setState("error");
          setErrorMsg(data.error ?? "Payment verification failed.");
        }
      })
      .catch((e) => {
        setState("error");
        setErrorMsg(e instanceof Error ? e.message : "Verification failed.");
      });
  }, [reference]);

  const handleCopy = () => {
    if (result?.code) {
      navigator.clipboard.writeText(result.code).then(() => {
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
      });
    }
  };

  if (state === "loading") {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center space-y-3">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600 mx-auto" />
          <p className="text-gray-500 text-sm">Verifying your payment...</p>
        </div>
      </div>
    );
  }

  if (state === "error") {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div className="bg-white rounded-lg border border-red-200 p-8 max-w-md text-center">
          <div className="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg className="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
          <h2 className="text-lg font-bold text-gray-900 mb-2">Verification Failed</h2>
          <p className="text-sm text-gray-500 mb-4">{errorMsg}</p>
          <Link
            to={`/purchase/${slug}`}
            className="text-sm text-amber-600 hover:text-amber-700 font-medium">
            ← Try again
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="text-center space-y-8">
          {/* Success icon */}
          <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
            <svg className="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
          </div>

          <div>
            <h1 className="text-2xl font-bold text-gray-900">Payment Successful!</h1>
            <p className="mt-2 text-gray-600">
              Your{" "}
              <strong>{result?.product_name ?? "product"}</strong> license has
              been created.
            </p>
          </div>

          {/* License code display */}
          <div className="bg-white rounded-lg border-2 border-green-200 p-6 shadow-sm">
            <p className="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">
              Your License Code
            </p>
            <div className="flex items-center justify-center space-x-3">
              <code className="text-2xl font-mono font-bold text-gray-900 tracking-wider select-all">
                {result?.code}
              </code>
              <button
                onClick={handleCopy}
                className="flex-shrink-0 p-2 rounded-md bg-gray-100 hover:bg-gray-200 transition-colors"
                title="Copy to clipboard">
                {copied ? (
                  <svg className="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                  </svg>
                ) : (
                  <svg className="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/>
                  </svg>
                )}
              </button>
            </div>
          </div>

          {/* Details */}
          <div className="bg-gray-50 rounded-lg p-4 text-left space-y-2">
            <div className="flex justify-between text-sm">
              <span className="text-gray-500">Product</span>
              <span className="font-medium text-gray-900">{result?.product_name}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-gray-500">Duration</span>
              <span className="font-medium text-gray-900">
                {result?.duration_days} day{(result?.duration_days ?? 1) > 1 ? "s" : ""}
              </span>
            </div>
          </div>

          {/* Instructions */}
          <div className="text-sm text-gray-500 space-y-2">
            <p>📋 <strong>Save this code</strong> — you'll need it to activate your license.</p>
            <p>📧 A copy has also been sent to your email.</p>
            <p>
              Provide this code to your site administrator to activate the{" "}
              {result?.product_name} license.
            </p>
          </div>

          <Link
            to="/"
            className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
            ← Back to Home
          </Link>
        </div>
      </div>
    </div>
  );
};

export default PurchaseSuccess;
