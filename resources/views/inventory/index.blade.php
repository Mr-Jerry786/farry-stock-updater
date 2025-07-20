@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-2xl font-bold">Amazon Inventory</h2>
            <a href="{{ route('inventory.upload.form') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                + Upload Inventory / Stock
            </a>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex space-x-4 text-sm font-medium" aria-label="Tabs">
                <a href="{{ route('inventory.index') }}"
                   class="{{ request()->routeIs('inventory.index') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-blue-600' }} px-3 py-2">
                    ✅ Inventory
                </a>
                <a href="{{ route('inventory.missing') }}"
                   class="{{ request()->routeIs('inventory.missing') ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-red-600' }} px-3 py-2">
                    ❌ Missing Inventory
                </a>
                <a href="{{ route('inventory.missing') }}"
                   class="{{ request()->is('inventory/missing') ? 'text-yellow-600 border-b-2 border-yellow-600' : 'text-gray-600 hover:text-yellow-600' }} px-3 py-2">
                    🕵️ Not in Inventory
                </a>
            </nav>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search and Export Controls -->
    <div class="flex flex-wrap items-center justify-between mb-4 gap-4">
        <form method="GET" class="w-full md:w-auto flex gap-2">
            <input type="text" name="search" placeholder="Search ASIN or EAN"
                   value="{{ request('search') }}"
                   class="px-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                🔍 Search
            </button>
        </form>

        <div class="flex gap-2">
            <a href="{{ route('inventory.export.instock') }}"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                ✅ Export In Stock
            </a>
            <a href="{{ route('inventory.export.outofstock') }}"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                ❌ Export Out of Stock
            </a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full table-auto border-collapse">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <tr>
                    <th class="px-6 py-3 border-b">ASIN</th>
                    <th class="px-6 py-3 border-b">EAN</th>
                    <th class="px-6 py-3 border-b">Stock</th>
                    <th class="px-6 py-3 border-b">Price (£)</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 border-b">{{ $item->asin }}</td>
                        <td class="px-6 py-4 border-b">{{ $item->ean }}</td>
                        <td class="px-6 py-4 border-b">{{ $item->stock }}</td>
                        <td class="px-6 py-4 border-b">£{{ number_format($item->price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No inventory found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $inventory->links('pagination::tailwind') }}
    </div>
@endsection
