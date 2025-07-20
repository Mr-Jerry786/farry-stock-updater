@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6">📤 Upload Amazon Inventory or Stock File</h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded shadow">
            {{ session('error') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded shadow">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload Form --}}
    <form action="{{ route('inventory.upload.handle') }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white p-6 rounded shadow-md w-full max-w-xl">
        @csrf

        {{-- File Type --}}
        <div class="mb-4">
            <label for="type" class="block font-medium text-gray-700 mb-2">Select Upload Type</label>
            <select name="type" id="type" required
                    class="w-full border border-gray-300 rounded px-4 py-2">
                <option value="">-- Choose Type --</option>
                <option value="inventory" {{ old('type') === 'inventory' ? 'selected' : '' }}>
                    Inventory (Add new items)
                </option>
                <option value="stock" {{ old('type') === 'stock' ? 'selected' : '' }}>
                    Stock (Update stock/prices)
                </option>
            </select>
        </div>

        {{-- File Upload --}}
        <div class="mb-4">
            <label for="file" class="block font-medium text-gray-700 mb-2">
                Upload File (.csv, .xlsx, .txt)
            </label>
            <input type="file" name="file" id="file" required
                   accept=".csv,.xlsx,.xls,.txt"
                   class="w-full border border-gray-300 rounded px-4 py-2">
            <p class="text-sm text-gray-500 mt-1">
                Supported formats: .csv, .xlsx, .xls, .txt
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('inventory.index') }}"
               class="text-sm text-blue-600 hover:underline">← Back to Inventory</a>
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                🚀 Upload
            </button>
        </div>
    </form>
@endsection
