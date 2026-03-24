<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Data Visualization Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            letter-spacing: -0.5px;
        }
        body {
            background: linear-gradient(135deg, #f5f3ff 0%, #f0f9ff 100%);
            min-height: 100vh;
        }
        .sidebar {
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        .main-content {
            transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .main-content.expanded {
            margin-left: 0 !important;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.4);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.6);
        }
        /* Smooth hover effects */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(90deg, #f59e0b, #ec4899);
            transform: translateX(-3px);
            transition: transform 0.3s ease;
        }
        .nav-link:hover::before {
            transform: translateX(0);
        }
        /* Card hover effect */
        .card-hover {
            transition: all 0.4s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
        }
        /* Stat cards with gradient background */
        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 193, 7, 0.1) 0%, transparent 70%);
            animation: float-slow 6s ease-in-out infinite;
        }
        @keyframes float-slow {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 20px); }
        }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-64 min-h-screen fixed z-20">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h1 class="text-white text-2xl font-bold bg-gradient-to-r from-amber-300 via-orange-300 to-rose-300 bg-clip-text text-transparent">
                    Vizualize
                </h1>
                <button id="closeSidebar" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <!-- Data Source Toggle -->
            <div class="px-6 py-5 border-b border-white/10">
                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wide mb-3">Pilih Data</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('weather.dashboard') }}" class="py-2 px-3 text-center text-xs font-medium rounded-lg transition-all {{ request()->routeIs('weather.*') ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-500/30' : 'bg-white/10 text-gray-300 hover:bg-white/20' }}">
                        🌤️ Cuaca
                    </a>
                    <a href="{{ route('student-productivity.dashboard') }}" class="py-2 px-3 text-center text-xs font-medium rounded-lg transition-all {{ request()->routeIs('student-productivity.*') ? 'bg-gradient-to-r from-violet-500 to-purple-500 text-white shadow-lg shadow-purple-500/30' : 'bg-white/10 text-gray-300 hover:bg-white/20' }}">
                        📊 Produktivitas
                    </a>
                </div>
            </div>

            <nav class="mt-2 flex flex-col space-y-1">
                @if(request()->routeIs('weather.*'))
                    <!-- Weather API Navigation -->
                    <a href="{{ route('weather.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('weather.dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <span>Dashboard Cuaca</span>
                    </a>
                    <a href="{{ route('weather.cities') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('weather.cities') ? 'bg-white/10 text-white' : '' }}">
                        <span>Kota-Kota</span>
                    </a>
                    <a href="{{ route('weather.forecast') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('weather.forecast') ? 'bg-white/10 text-white' : '' }}">
                        <span>Prakiraan 7 Hari</span>
                    </a>
                    <a href="{{ route('weather.comparison') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('weather.comparison') ? 'bg-white/10 text-white' : '' }}">
                        <span>Perbandingan Kota</span>
                    </a>
                @elseif(request()->routeIs('student-productivity.*'))
                    <a href="{{ route('student-productivity.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('student-productivity.dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <span>Dashboard Produktivitas</span>
                    </a>
                @else
                    <a href="{{ route('weather.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('weather.dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <span>Dashboard Cuaca</span>
                    </a>
                    <a href="{{ route('student-productivity.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('student-productivity.dashboard') ? 'bg-white/10 text-white' : '' }}">
                        <span>Dataset Produktivitas</span>
                    </a>
                @endif
            </nav>

            <!-- Footer Info -->
            @if(request()->routeIs('weather.*'))
            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                    <p class="text-gray-400 text-xs mb-2">Data dari:</p>
                    <a href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer" class="text-amber-300 hover:text-amber-200 underline text-xs transition">
                        Open-Meteo API
                    </a>
                </div>
            </div>
            @elseif(request()->routeIs('student-productivity.*'))
            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                    <p class="text-gray-400 text-xs mb-2">Sumber Data:</p>
                    <p class="text-amber-300 text-xs break-all font-medium">
                        ultimate_student_productivity_dataset_5000.csv
                    </p>
                </div>
            </div>
            @endif
        </aside>

        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden"></div>

        <!-- Main Content -->
        <main id="mainContent" class="main-content flex-1 ml-64 p-8 min-h-screen">
            <!-- Top Bar with Toggle -->
            <div class="flex items-center justify-between mb-8">
                <button id="toggleSidebar" class="bg-white/10 hover:bg-white/20 text-white p-2.5 rounded-lg transition-colors backdrop-blur-sm border border-white/10">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                
                <div class="flex items-center space-x-3">
                    @if(request()->routeIs('weather.*'))
                        <span class="text-sm font-medium bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 px-4 py-2 rounded-full">
                            🌤️ Mode Cuaca
                        </span>
                    @elseif(request()->routeIs('student-productivity.*'))
                        <span class="text-sm font-medium bg-gradient-to-r from-violet-100 to-purple-100 text-purple-700 px-4 py-2 rounded-full">
                            📊 Mode Produktivitas
                        </span>
                    @else
                        <span class="text-sm font-medium bg-gradient-to-r from-gray-100 to-slate-100 text-slate-700 px-4 py-2 rounded-full">
                            📈 Data Mode
                        </span>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-5 py-4 rounded-lg mb-6 flex items-start space-x-3">
                    <span class="text-xl">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-5 py-4 rounded-lg mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        // Check localStorage for sidebar state
        const sidebarState = localStorage.getItem('sidebarCollapsed');
        if (sidebarState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        toggleBtn.addEventListener('click', toggleSidebar);
        
        if (closeBtn) {
            closeBtn.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                overlay.classList.add('hidden');
            });
        }

        // Handle responsive
        function handleResize() {
            if (window.innerWidth < 1024) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        }

        window.addEventListener('resize', handleResize);
    </script>

    @stack('scripts')
</body>
</html>