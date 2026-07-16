@extends('layouts.purchase')

@section('title', 'Bolakaz Licensing')

@section('content')
<div class="text-center space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">⚡ Bolakaz Licensing</h1>
    <p class="text-gray-600 max-w-md mx-auto">
        Purchase license codes for Bolakaz products. Select a product below to get started.
    </p>

    @if ($products->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 max-w-md mx-auto">
            <p class="text-yellow-800">No products are currently available for purchase.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
            @foreach ($products as $product)
                <a href="{{ route('purchase.show', ['product' => $product->slug]) }}"
                   class="block bg-white rounded-lg border border-gray-200 p-6 text-left hover:border-amber-500 hover:shadow-md transition-all group">
                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">
                        {{ $product->name }}
                    </h2>
                    @if ($product->description)
                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($product->description, 100) }}</p>
                    @endif
                    <p class="mt-3 text-sm text-gray-500">
                        From <span class="font-semibold text-gray-900">₦{{ number_format($product->daily_rate, 2) }}</span>/day
                    </p>
                    <span class="mt-3 inline-flex items-center text-sm font-medium text-amber-600 group-hover:text-amber-700">
                        Purchase License →
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
