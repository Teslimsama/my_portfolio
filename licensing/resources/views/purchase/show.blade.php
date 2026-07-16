@extends('layouts.purchase')

@section('title', "Purchase {$product->name} License")

@section('content')
<div class="space-y-8">
    <!-- Product Info -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
        @if ($product->description)
            <p class="mt-2 text-gray-600">{{ $product->description }}</p>
        @endif
        <p class="mt-2 text-sm text-gray-500">
            Base rate: <span class="font-semibold text-gray-900">₦{{ number_format($product->daily_rate, 2) }}</span> / day
        </p>
    </div>

    <!-- Tier Selection -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Select a Plan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($tiers as $tier)
                @php
                    $isCustom = $tier->tier_key === 'custom';
                    $days = $tier->duration_days;
                    $original = $product->daily_rate * ($days ?: 1);
                    $discounted = $original * (1 - $tier->discount_pct / 100);
                @endphp

                <div class="tier-card relative rounded-lg border-2 p-4 cursor-pointer transition-all hover:border-amber-500 hover:shadow-md {{ $isCustom ? 'border-dashed border-gray-300' : 'border-gray-200' }}"
                     data-tier-id="{{ $tier->id }}"
                     data-tier-key="{{ $tier->tier_key }}">
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $isCustom ? 'Custom' : ($days . ' Days') }}
                        </h3>
                        @if ($tier->discount_pct > 0)
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                {{ (int) $tier->discount_pct }}% OFF
                            </span>
                        @endif
                        <p class="mt-2 text-2xl font-bold text-amber-600">
                            {{ $isCustom ? '₦' . number_format($product->daily_rate, 2) . '/day' : '₦' . number_format($discounted, 2) }}
                        </p>
                        @if (! $isCustom && $tier->discount_pct > 0)
                            <p class="text-sm text-gray-400 line-through">₦{{ number_format($original, 2) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Custom Days Input (hidden by default) -->
    <div id="custom-days-section" class="hidden">
        <label for="custom_days" class="block text-sm font-medium text-gray-700">
            Number of Days
        </label>
        <div class="mt-1 flex rounded-md shadow-sm max-w-xs">
            <input type="number" id="custom_days" name="custom_days" min="1" max="365" value="1"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm px-3 py-2 border">
            <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                days
            </span>
        </div>
        <p id="custom-price-display" class="mt-2 text-sm text-gray-600">
            Total: <span class="font-bold text-amber-600">₦{{ number_format($product->daily_rate, 2) }}</span>
        </p>
    </div>

    <!-- Email Input -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
            Email Address
        </label>
        <p class="text-xs text-gray-500 mb-1">Your license code will be sent here.</p>
        <input type="email" id="email" name="email" required
               placeholder="you@example.com"
               class="block w-full max-w-md rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm px-3 py-2 border">
    </div>

    <!-- Pay Button -->
    <div>
        <form id="payment-form" method="POST" action="{{ route('purchase.initiate', ['product' => $product->slug]) }}">
            @csrf
            <input type="hidden" name="tier_id" id="tier_id" value="">
            <input type="hidden" name="custom_days" id="form_custom_days" value="">
            <input type="hidden" name="email" id="form_email" value="">

            <button type="submit" id="pay-button"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                Select a plan to continue
            </button>
        </form>
        <p class="mt-2 text-xs text-gray-400">
            🔒 Payments are securely processed by Paystack.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const cards = document.querySelectorAll('.tier-card');
    const customSection = document.getElementById('custom-days-section');
    const customDaysInput = document.getElementById('custom_days');
    const customPriceDisplay = document.getElementById('custom-price-display');
    const payButton = document.getElementById('pay-button');
    const tierIdInput = document.getElementById('tier_id');
    const formCustomDays = document.getElementById('form_custom_days');
    const formEmail = document.getElementById('form_email');
    const emailInput = document.getElementById('email');
    const dailyRate = {{ $product->daily_rate }};

    let selectedTierKey = null;
    let selectedTierId = null;

    cards.forEach(card => {
        card.addEventListener('click', () => {
            // Deselect all
            cards.forEach(c => c.classList.remove('border-amber-500', 'bg-amber-50'));
            // Select this
            card.classList.add('border-amber-500', 'bg-amber-50');

            selectedTierId = card.dataset.tierId;
            selectedTierKey = card.dataset.tierKey;
            tierIdInput.value = selectedTierKey === 'custom' ? '' : selectedTierId;

            if (selectedTierKey === 'custom') {
                customSection.classList.remove('hidden');
                formCustomDays.value = customDaysInput.value;
                updateCustomPrice();
                payButton.textContent = 'Pay ₦' + (dailyRate * customDaysInput.value).toFixed(2).replace(
                    /\B(?=(\d{3})+(?!\d))/g, ',') + ' with Paystack';
                payButton.disabled = false;
            } else {
                customSection.classList.add('hidden');
                formCustomDays.value = '';
                const priceEl = card.querySelector('.text-amber-600');
                const priceText = priceEl ? priceEl.textContent.replace('₦', '').replace(/,/g, '') :
                    '0';
                payButton.textContent = 'Pay ' + (priceEl ? priceEl.textContent.trim() : '') +
                    ' with Paystack';
                payButton.disabled = false;
            }
        });
    });

    customDaysInput.addEventListener('input', () => {
        formCustomDays.value = customDaysInput.value;
        updateCustomPrice();
        const total = dailyRate * parseInt(customDaysInput.value || 1);
        payButton.textContent = 'Pay ₦' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') +
            ' with Paystack';
    });

    emailInput.addEventListener('input', () => {
        formEmail.value = emailInput.value;
    });

    function updateCustomPrice() {
        const days = parseInt(customDaysInput.value || 1);
        const total = dailyRate * days;
        customPriceDisplay.innerHTML = 'Total: <span class="font-bold text-amber-600">₦' +
            total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '</span>';
    }

    // Form submission: copy email before submit
    document.getElementById('payment-form').addEventListener('submit', () => {
        formEmail.value = emailInput.value;
    });
})();
</script>
@endpush
