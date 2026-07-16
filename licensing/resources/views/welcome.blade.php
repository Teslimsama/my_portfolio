<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bolakaz Licensing API</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center space-y-6 max-w-lg mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-900">⚡ Bolakaz Licensing</h1>
        <p class="text-gray-600">
            This is the licensing API server. Purchase pages are served by the main site.
        </p>
        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-3 text-left">
            <h2 class="font-semibold text-gray-900">Available Products</h2>
            @if ($products->isEmpty())
                <p class="text-sm text-gray-500">No products configured yet.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($products as $product)
                        <li class="text-sm flex justify-between items-center">
                            <span class="font-medium">{{ $product->name }}</span>
                            <span class="text-gray-500">₦{{ number_format($product->daily_rate, 2) }}/day</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <a href="/admin" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors text-sm font-medium">
            Admin Panel →
        </a>
    </div>
</body>
</html>
