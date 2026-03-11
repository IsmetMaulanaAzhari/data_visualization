<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sales Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 min-h-screen fixed">
            <div class="p-4">
                <h1 class="text-white text-xl font-bold">
                    <i class="fas fa-chart-line mr-2"></i>Sales Dashboard
                </h1>
            </div>
            <nav class="mt-4">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('categories.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-tags w-6"></i>
                    <span>Categories</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('products.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-box w-6"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('customers.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-users w-6"></i>
                    <span>Customers</span>
                </a>
                <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white {{ request()->routeIs('orders.*') ? 'bg-slate-700 text-white border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-shopping-cart w-6"></i>
                    <span>Orders</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>