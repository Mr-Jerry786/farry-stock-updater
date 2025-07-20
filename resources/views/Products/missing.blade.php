@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-red-600">❌ Missing Inventory</h1>
        @if($missing->count())
            <a href="{{ route('inventory.export.missing') }}"
               class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">
                ⬇️ Export CSV
            </a>
        @endif
    </div>

    {{-- Toast Notification --}}
    @if(session('success') || session('error'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show"
            class="fixed top-4 right-4 z-50 px-4 py-3 rounded shadow-lg text-white transition"
            :class="'{{ session('error') ? 'bg-red-500' : 'bg-green-500' }}'"
        >
            {{ session('error') ?? session('success') }}
        </div>
    @endif

    {{-- Date Filter --}}
    <form method="GET" action="{{ route('inventory.missing') }}" class="mb-4 flex flex-wrap gap-3 items-center">
        <div>
            <label class="text-sm text-gray-700 block mb-1">From:</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="border px-3 py-2 rounded w-full">
        </div>
        <div>
            <label class="text-sm text-gray-700 block mb-1">To:</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="border px-3 py-2 rounded w-full">
        </div>
        <button type="submit"
                class="mt-5 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Filter
        </button>
        @if(request()->has('start_date') || request()->has('end_date'))
            <a href="{{ route('inventory.missing') }}"
               class="mt-5 text-sm text-gray-600 underline ml-2">Clear Filter</a>
        @endif
    </form>

    @if($missing->count())
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full table-auto border">
                <thead class="bg-yellow-100">
                    <tr>
                        <th class="px-4 py-2 border text-left">ASIN</th>
                        <th class="px-4 py-2 border text-left">EAN</th>
                        <th class="px-4 py-2 border text-left">Stock</th>
                        <th class="px-4 py-2 border text-left">Price</th>
                        <th class="px-4 py-2 border text-left">Discount</th>
                        <th class="px-4 py-2 border text-left">Detected</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($missing as $item)
                        @php
                            $detectedAt = \Carbon\Carbon::parse($item->created_at ?? now()->subDay());
                            $isRecent = $detectedAt->gt(now()->subHour());
                        @endphp
                        <tr class="{{ $isRecent ? 'bg-yellow-50' : '' }}">
                            <td class="px-4 py-2 border">{{ $item->asin ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->ean ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $item->stock ?? 0 }}</td>
                            <td class="px-4 py-2 border">£{{ number_format($item->price ?? 0, 2) }}</td>
                            <td class="px-4 py-2 border">£{{ number_format($item->discount ?? 0, 2) }}</td>
                            <td class="px-4 py-2 border text-sm text-gray-600">
                                {{ $detectedAt->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $missing->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @else
        <p class="text-gray-500">No missing inventory entries found.</p>
    @endif
</div>
@endsection
