@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-2xl font-bold text-red-600">❌ Missing Inventory</h2>
            <div class="flex space-x-2">
                <a href="{{ route('inventory.export.missing') }}"
                   class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition">
                    ⬇️ Export Missing Inventory
                </a>
                <a href="{{ route('inventory.upload.form') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Upload Inventory / Stock
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-gray-200 mb-4">
            <nav class="flex space-x-4 text-sm font-medium" aria-label="Tabs">
                <a href="{{ route('inventory.index') }}"
                   class="{{ request()->routeIs('inventory.index') ? 'text-blue-600 border-b-2 border-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }} px-3 py-2">
                    ✅ Inventory
                </a>
                <a href="{{ route('inventory.missing') }}"
                   class="{{ request()->routeIs('inventory.missing') ? 'text-red-600 border-b-2 border-red-600 font-semibold' : 'text-gray-600 hover:text-red-600' }} px-3 py-2">
                    ❌ Missing Inventory
                </a>
                <a href="{{ route('inventory.missing') }}"
                   class="{{ request()->routeIs('inventory.missing') ? 'text-yellow-600 border-b-2 border-yellow-600 font-semibold' : 'text-gray-600 hover:text-yellow-600' }} px-3 py-2">
                    🕵️ Not in Inventory
                </a>
            </nav>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('inventory.missing') }}" class="mb-4">
            <div class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by ASIN or EAN"
                       class="w-64 px-4 py-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                    🔍 Search
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full table-auto border-collapse">
            <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                <tr>
                    <th class="px-6 py-3 border-b">ASIN</th>
                    <th class="px-6 py-3 border-b">EAN</th>
                    <th class="px-6 py-3 border-b">Stock</th>
                    <th class="px-6 py-3 border-b">Price (£)</th>
                    <th class="px-6 py-3 border-b">Uploaded At</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
                @forelse($missingInventory as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 border-b">{{ $item->asin }}</td>
                        <td class="px-6 py-4 border-b">{{ $item->ean }}</td>
                        <td class="px-6 py-4 border-b">{{ $item->stock ?? 'N/A' }}</td>
                        <td class="px-6 py-4 border-b">£{{ $item->price ? number_format($item->price, 2) : 'N/A' }}</td>
                        <td class="px-6 py-4 border-b">{{ $item->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No missing inventory found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $missingInventory->links('pagination::tailwind') }}
    </div>
@endsection
