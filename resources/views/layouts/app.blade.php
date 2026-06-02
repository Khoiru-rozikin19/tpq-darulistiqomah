<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Keuangan') - Madrasah Darul Istiqomah</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    @yield('styles')
</head>
<body class="h-full bg-slate-50/50 flex overflow-hidden">
    
    <!-- Sidebar (Left) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:flex-shrink-0 bg-gradient-to-b from-blue-900 via-blue-950 to-slate-950 text-white border-r border-blue-800/20 relative z-30">
        <!-- Sidebar Brand -->
        <div class="h-20 flex items-center px-6 border-b border-white/5 gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Istiqomah" class="w-9 h-9 object-contain bg-white/10 p-1 rounded-lg">
            <div>
                <h1 class="text-sm font-bold tracking-tight text-white leading-tight heading-font">Darul Istiqomah</h1>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-400">Sistem Keuangan</p>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('santri.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('santri.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Data Santri
            </a>

            <a href="{{ route('spp.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('spp.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pembayaran SPP
            </a>

            <a href="{{ route('kas.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('kas.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Transaksi KAS
            </a>

            <a href="{{ route('pegawai.index') }}" 
               class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('pegawai.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20 font-semibold' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Daftar Pegawai
            </a>
        </nav>

        <!-- Sidebar Footer / Profile -->
        <div class="p-4 border-t border-white/5 bg-slate-950/40">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 font-bold heading-font uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-sm font-semibold truncate text-white leading-tight">{{ Auth::user()->name }}</h4>
                    <span class="text-[10px] font-semibold text-blue-400 uppercase tracking-wider">
                        {{ [
                            'admin_tu' => 'Admin TU',
                            'kepala_madrasah' => 'Kepala Madrasah',
                            'guru' => 'Guru',
                            'bendahara' => 'Bendahara'
                        ][Auth::user()->role] ?? ucwords(str_replace('_', ' ', Auth::user()->role)) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-xs font-semibold text-rose-200 hover:text-white bg-rose-950/20 hover:bg-rose-600 border border-rose-500/10 rounded-xl transition-all duration-150">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar Aplikasi
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Top Navigation Header -->
    <div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-gradient-to-r from-blue-900 to-indigo-950 text-white flex items-center justify-between px-6 z-40 border-b border-white/5 shadow-md">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Istiqomah" class="w-8 h-8 object-contain bg-white/10 p-0.5 rounded-lg">
            <h1 class="text-sm font-bold heading-font">Darul Istiqomah</h1>
        </div>
        
        <!-- Toggle button -->
        <button id="mobile-menu-btn" class="p-1 rounded-lg hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 lg:hidden hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="mobile-sidebar-backdrop"></div>
        
        <!-- Drawer content -->
        <div class="fixed inset-y-0 left-0 w-72 bg-gradient-to-b from-blue-900 to-slate-950 text-white flex flex-col justify-between z-50 transform transition-transform duration-300 -translate-x-full" id="mobile-sidebar-panel">
            <div>
                <div class="h-16 flex items-center justify-between px-6 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Istiqomah" class="w-8 h-8 object-contain bg-white/10 p-0.5 rounded-lg">
                        <h1 class="text-sm font-bold heading-font">Darul Istiqomah</h1>
                    </div>
                    <button id="mobile-menu-close-btn" class="p-1 rounded-lg hover:bg-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <nav class="px-4 py-6 space-y-1.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('santri.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('santri.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Data Santri
                    </a>
                    <a href="{{ route('spp.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('spp.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Pembayaran SPP
                    </a>
                    <a href="{{ route('kas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('kas.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Transaksi KAS
                    </a>
                    <a href="{{ route('pegawai.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 {{ Request::routeIs('pegawai.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-white/5' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Daftar Pegawai
                    </a>
                </nav>
            </div>
            
            <div class="p-4 border-t border-white/5 bg-slate-950/40">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 font-bold heading-font">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-xs font-semibold truncate text-white">{{ Auth::user()->name }}</h4>
                        <span class="text-[9px] font-semibold text-blue-400 uppercase tracking-wider">
                            {{ [
                                'admin_tu' => 'Admin TU',
                                'kepala_madrasah' => 'Kepala Madrasah',
                                'guru' => 'Guru',
                                'bendahara' => 'Bendahara'
                            ][Auth::user()->role] ?? ucwords(str_replace('_', ' ', Auth::user()->role)) }}
                        </span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-xs font-semibold text-rose-200 hover:text-white bg-rose-950/20 hover:bg-rose-600 rounded-xl transition-all duration-150">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="flex-1 flex flex-col overflow-hidden pt-16 lg:pt-0">
        
        <!-- Header (Desktop Only) -->
        <header class="hidden lg:flex h-20 items-center justify-between px-8 bg-white border-b border-slate-100 flex-shrink-0 z-20">
            <div>
                <h2 class="text-xl font-bold text-slate-800 heading-font">@yield('page_title', 'Keuangan')</h2>
                <p class="text-xs text-slate-400">Madrasah Darul Istiqomah &middot; Hari Ini: {{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- User Profile Dropdown / Badge -->
                <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 border border-slate-100 rounded-2xl">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-slate-600">Sesi Aktif: <strong class="text-slate-800">{{ [
                        'admin_tu' => 'Admin TU',
                        'kepala_madrasah' => 'Kepala',
                        'guru' => 'Guru',
                        'bendahara' => 'Bendahara'
                    ][Auth::user()->role] ?? ucwords(str_replace('_', ' ', Auth::user()->role)) }}</strong></span>
                </div>
            </div>
        </header>

        <!-- Main Content Scroll Area -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8 relative">
            <!-- Toast Notifications -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-2xl flex items-center justify-between text-emerald-800 text-sm shadow-sm transition-all duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-2xl flex items-center justify-between text-rose-800 text-sm shadow-sm transition-all duration-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Script for mobile menu logic -->
    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenuCloseBtn = document.getElementById('mobile-menu-close-btn');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarBackdrop = document.getElementById('mobile-sidebar-backdrop');
        const mobileSidebarPanel = document.getElementById('mobile-sidebar-panel');

        function openMobileMenu() {
            mobileSidebar.classList.remove('hidden');
            setTimeout(() => {
                mobileSidebarPanel.classList.remove('-translate-x-full');
            }, 50);
        }

        function closeMobileMenu() {
            mobileSidebarPanel.classList.add('-translate-x-full');
            setTimeout(() => {
                mobileSidebar.classList.add('hidden');
            }, 300);
        }

        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileMenu);
        if (mobileMenuCloseBtn) mobileMenuCloseBtn.addEventListener('click', closeMobileMenu);
        if (mobileSidebarBackdrop) mobileSidebarBackdrop.addEventListener('click', closeMobileMenu);
    </script>
    @yield('scripts')
</body>
</html>
