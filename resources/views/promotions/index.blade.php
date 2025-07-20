@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">🎯 Promotion Products</h1>
        {{-- Optional future feature --}}
        {{-- <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            + Add Promotion
        </a> --}}
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full table-auto border-collapse">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <tr>
                    <th class="px-6 py-3 border-b">ASIN</th>
                    <th class="px-6 py-3 border-b">EAN</th>
                    <th class="px-6 py-3 border-b">Title</th>
                    <th class="px-6 py-3 border-b">Price (£)</th>
                    <th class="px-6 py-3 border-b">Discount (£)</th>
                    <th class="px-6 py-3 border-b">Stock</th>
                    <th class="px-6 py-3 border-b">Ends On</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @forelse($promotions as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 border-b">{{ $product->asin ?? '—' }}</td>
                        <td class="px-6 py-4 border-b">{{ $product->ean ?? '—' }}</td>
                        <td class="px-6 py-4 border-b">{{ $product->title }}</td>
                        <td class="px-6 py-4 border-b">£{{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4 border-b">£{{ number_format($product->discount, 2) }}</td>
                        <td class="px-6 py-4 border-b">{{ $product->stock }}</td>
                        <td class="px-6 py-4 border-b">{{ $product->promotion_end_date ? \Carbon\Carbon::parse($product->promotion_end_date)->format('Y-m-d') : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No promotion products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $promotions->links('pagination::tailwind') }}
    </div>
</div>
@endsection
