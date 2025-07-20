@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">📦 Inventory Dashboard</h1>
        <a href="{{ route('upload.form') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            ⬆️ Upload File
        </a>
    </div>

    {{-- Toast Notifications --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="bg-green-100 text-green-800 p-4 rounded mb-6 transition-all duration-500">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="bg-red-100 text-red-800 p-4 rounded mb-6 transition-all duration-500">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search Form --}}
    <form method="GET" action="{{ route('products.index') }}" class="mb-4 flex flex-wrap gap-2 items-center">
        <input type="text" name="search" placeholder="Search by ASIN or EAN" value="{{ request('search') }}"
               class="border rounded px-3 py-2 w-full md:w-1/3">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">🔍 Search</button>
        <a href="{{ route('products.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Reset</a>

        {{-- Links to Missing & New Products --}}
        <div class="ml-auto flex gap-2">
            <a href="{{ route('products.new') }}"
               class="text-sm text-blue-600 underline hover:text-blue-800">🆕 View New Products</a>
            <a href="{{ route('products.missing') }}"
               class="text-sm text-red-600 underline hover:text-red-800">❌ View Missing Products</a>
        </div>
    </form>

    {{-- Export Buttons --}}
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('products.export.outofstock') }}"
           class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">
            ⬇️ Export Out of Stock
        </a>
        <a href="{{ route('products.export.instock') }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
            ⬇️ Export In Stock
        </a>
        <a href="{{ route('products.missing.export') }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 text-sm">
            ⬇️ Export Missing Products
        </a>
    </div>

    {{-- Inventory Table --}}
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full table-auto border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">ASIN</th>
                    <th class="px-4 py-2 border">EAN</th>
                    <th class="px-4 py-2 border">Price</th>
                    <th class="px-4 py-2 border">Discount</th>
                    <th class="px-4 py-2 border">Stock</th>
                    <th class="px-4 py-2 border">Updated</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $isRecent = now()->diffInMinutes($product->updated_at) <= 60;
                    @endphp
                    <tr class="{{ $isRecent ? 'bg-green-50' : '' }}">
                        <td class="px-4 py-2 border">{{ $product->asin }}</td>
                        <td class="px-4 py-2 border">{{ $product->ean }}</td>
                        <td class="px-4 py-2 border">£{{ number_format($product->price, 2) }}</td>
                        <td class="px-4 py-2 border">{{ number_format($product->discount, 2) }}</td>
                        <td class="px-4 py-2 border">
                            <form method="POST" action="{{ route('products.update', $product) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="number" name="stock" value="{{ $product->stock }}"
                                       class="w-20 px-2 py-1 border rounded text-sm">
                                <button type="submit" class="bg-green-500 text-white px-2 py-1 text-xs rounded">Update</button>
                            </form>
                        </td>
                        <td class="px-4 py-2 border">{{ $product->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
