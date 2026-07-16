@extends('layouts.purchase')

@section('title', 'License Code — ' . $productName)

@section('content')
<div class="max-w-lg mx-auto text-center space-y-8">
    <!-- Success Icon -->
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payment Successful!</h1>
        <p class="mt-2 text-gray-600">
            Your <strong>{{ $productName }}</strong> license has been created.
        </p>
    </div>

    <!-- License Code Display -->
    <div class="bg-white rounded-lg border-2 border-green-200 p-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Your License Code</p>
        <div class="flex items-center justify-center space-x-3">
            <code class="text-2xl font-mono font-bold text-gray-900 tracking-wider select-all">{{ $code }}</code>
            <button onclick="copyCode()"
                    class="flex-shrink-0 p-2 rounded-md bg-gray-100 hover:bg-gray-200 transition-colors"
                    title="Copy to clipboard">
                <svg id="copy-icon" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/>
                </svg>
                <svg id="check-icon" class="hidden h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Details -->
    <div class="bg-gray-50 rounded-lg p-4 text-left space-y-2">
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Product</span>
            <span class="font-medium text-gray-900">{{ $productName }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-500">Duration</span>
            <span class="font-medium text-gray-900">{{ $durationDays }} day{{ $durationDays > 1 ? 's' : '' }}</span>
        </div>
    </div>

    <!-- Instructions -->
    <div class="text-sm text-gray-500 space-y-2">
        <p>📋 <strong>Save this code</strong> — you'll need it to activate your license.</p>
        <p>📧 A copy has also been sent to your email.</p>
        <p>Provide this code to your site administrator to activate the {{ $productName }} license.</p>
    </div>

    <a href="{{ route('home') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
        ← Back to Products
    </a>
</div>
@endsection

@push('scripts')
<script>
function copyCode() {
    const code = '{{ $code }}';
    navigator.clipboard.writeText(code).then(() => {
        document.getElementById('copy-icon').classList.add('hidden');
        document.getElementById('check-icon').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('check-icon').classList.add('hidden');
            document.getElementById('copy-icon').classList.remove('hidden');
        }, 2000);
    });
}
</script>
@endpush
