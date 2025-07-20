<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Amazon Stock File</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">📦 Upload Inventory or Stock File</h1>

        {{-- Success Toast --}}
        @if(session('success'))
            <div class="mb-4 text-green-700 bg-green-100 border border-green-300 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Toast --}}
        @if($errors->any())
            <div class="mb-4 text-red-700 bg-red-100 border border-red-300 p-3 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- File Type --}}
            <div>
                <label for="file_type" class="block font-semibold mb-1">Select File Type</label>
                <select name="file_type" id="file_type" required
                        class="w-full border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Choose Type --</option>
                    <option value="inventory">Inventory</option>
                    <option value="stock">Stock</option>
                </select>
            </div>

            {{-- File Input --}}
            <div>
                <label for="stock_file" class="block font-semibold mb-1">Choose CSV, TXT or XLSX File</label>
                <input type="file" name="stock_file" id="stock_file" required
                       accept=".csv,.txt,.xlsx"
                       class="w-full border border-gray-300 rounded px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Submit Button --}}
            <div class="text-center">
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-150">
                    Upload & Update
                </button>
            </div>
        </form>
    </div>

</body>
</html>
