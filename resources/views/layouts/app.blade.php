<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Amazon Stock Updater</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        danger: '#dc2626',
                        success: '#16a34a',
                        sidebar: '#f9fafb',
                        accent: '#1e40af',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800"
      x-data="{ toast: @json(session('success') ?? session('error') ?? false), toastType: '{{ session('error') ? 'error' : 'success' }}' }"
      x-init="if (toast) setTimeout(() => toast = false, 3500)">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-sidebar border-r border-gray-200 p-6 shadow-md space-y-8">
            <div>
                <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
                    📦 <span>Stock Updater</span>
                </h1>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('products.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-100 transition {{ request()->is('products*') ? 'bg-blue-200 text-blue-800 font-semibold' : 'text-gray-700' }}">
                    🗂️ <span>Dashboard</span>
                </a>
                <a href="{{ route('upload.form') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-100 transition {{ request()->is('upload') ? 'bg-blue-200 text-blue-800 font-semibold' : 'text-gray-700' }}">
                    ⬆️ <span>Upload File</span>
                </a>
                <a href="{{ route('inventory.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-100 transition {{ request()->is('inventory*') ? 'bg-blue-200 text-blue-800 font-semibold' : 'text-gray-700' }}">
                    📊 <span>Amazon Inventory</span>
                </a>
                <a href="{{ route('promotions.index') }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-blue-100 transition {{ request()->is('promotions*') ? 'bg-blue-200 text-blue-800 font-semibold' : 'text-gray-700' }}">
                    🎯 <span>Promotion Products</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-white shadow-inner rounded-tl-3xl">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notification -->
    @if(session('success') || session('error'))
        <div x-show="toast" x-transition
             x-bind:class="toastType === 'error' ? 'bg-danger' : 'bg-success'"
             class="fixed bottom-4 right-4 text-white px-5 py-3 rounded-lg shadow-lg text-sm z-50">
            {{ session('error') ?? session('success') }}
        </div>
    @endif

</body>
</html>
