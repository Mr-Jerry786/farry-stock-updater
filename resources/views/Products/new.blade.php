@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">🆕 New Products</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        @if($newProducts->count())
            <div>
                <a href="{{ route('products.new.export') }}" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    🔽 Download as CSV
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('products.transfer') }}">
            @csrf
            @if($newProducts->count())
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    🚀 Transfer to Inventory
                </button>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow mb-4">
        <table class="min-w-full table-auto border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">ASIN</th>
                    <th class="px-4 py-2 border">EAN</th>
                    <th class="px-4 py-2 border">Price</th>
                    <th class="px-4 py-2 border">Discount</th>
                    <th class="px-4 py-2 border">Stock</th>
                    <th class="px-4 py-2 border">Date Added</th>
                </tr>
            </thead>
            <tbody>
                @forelse($newProducts as $product)
                    <tr>
                        <td class="px-4 py-2 border">{{ $product->asin }}</td>
                        <td class="px-4 py-2 border">{{ $product->ean }}</td>
                        <td class="px-4 py-2 border">£{{ number_format($product->price, 2) }}</td>
                        <td class="px-4 py-2 border">
                            {{ number_format($product->discount, 2) == '0.00' ? '35.00' : number_format($product->discount, 2) }}
                        </td>
                        <td class="px-4 py-2 border">{{ $product->stock }}</td>
                        <td class="px-4 py-2 border">{{ $product->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">No new products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $newProducts->links() }}
    </div>
</div>
@endsection
