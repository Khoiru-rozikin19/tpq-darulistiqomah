<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Keuangan Madrasah Darul Istiqomah</title>
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
</head>
<body class="h-full flex items-center justify-center p-0 m-0">
    <div class="w-full h-full flex flex-col md:flex-row">
        <!-- Kolom Kiri: Form Login (White Background) -->
        <div class="w-full md:w-[45%] lg:w-[40%] bg-white flex flex-col justify-between p-8 lg:p-16 h-full overflow-y-auto">
            <!-- Header Logo -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Istiqomah" class="w-10 h-10 object-contain rounded-xl">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 leading-tight heading-font">Darul Istiqomah</h2>
                    <p class="text-xs font-semibold tracking-wider text-blue-600 uppercase">الموسسةدارالاستقامةقرأنية</p>
                </div>
            </div>

            <!-- Form Content -->
            <div class="my-auto py-10 max-w-sm w-full mx-auto">
                <h3 class="text-3xl font-extrabold text-slate-800 mb-2 heading-font">Selamat Datang</h3>
                <p class="text-slate-500 mb-8 text-sm">Silakan login untuk mengakses panel keuangan madrasah.</p>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email/Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="Masukkan email Anda" 
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm text-slate-800 placeholder-slate-400 bg-slate-50/50">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required placeholder="Masukkan password Anda" 
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm text-slate-800 placeholder-slate-400 bg-slate-50/50">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <label for="remember" class="ml-2 block text-sm text-slate-600 select-none">Ingat Saya</label>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/20 active:scale-[0.98] text-sm heading-font">
                        Login ke Panel
                    </button>
                </form>

                <div class="mt-8 text-center text-xs text-slate-400">
                    Sistem Keuangan Darul Istiqomah &copy; 2026.
                </div>
            </div>

            <!-- Footer Info -->
            <div class="text-xs text-slate-400 mt-auto pt-4 flex justify-between">
                <span>Versi 1.0.0</span>
                <span>Madrasah Darul Istiqomah</span>
            </div>
        </div>

        <!-- Kolom Kanan: Panel Info (Blue Gradient - Referencing Screenshot) -->
        <div class="hidden md:flex md:w-[55%] lg:w-[60%] bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-950 text-white p-12 lg:p-24 flex-col justify-between relative overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl -ml-20 -mb-20"></div>
            
            <div class="z-10">
                <!-- Decorative Icon Grid -->
                <div class="flex gap-2 mb-8">
                    <span class="text-2xl">🕌</span>
                    <span class="text-2xl">☪️</span>
                    <span class="text-2xl">📖</span>
                </div>
                
                <h1 class="text-4xl lg:text-5xl font-extrabold mb-6 leading-tight heading-font">Halo Guru & Pengelola!</h1>
                <p class="text-blue-100 text-base lg:text-lg leading-relaxed max-w-xl mb-8">
                    Selamat datang di sistem manajemen keuangan internal Madrasah Darul Istiqomah. 
                    Kelola pencatatan SPP santri, kas masuk/keluar, dan data santri dalam satu platform terintegrasi. 
                    Memudahkan transparansi dan akurasi keuangan madrasah.
                </p>

            </div>

            <!-- Contact Admin Info (Referencing Screenshot) -->
            <div class="z-10 mt-auto pt-8 border-t border-blue-600/40 flex flex-col sm:flex-row justify-between gap-4 text-xs text-blue-200">
                <div>
                    <span class="block text-blue-300 uppercase tracking-wider font-semibold mb-1">Kontak Admin TU</span>
                    <span class="block text-white font-medium">082372838757 - WhatsApp</span>
                </div>
                <div>
                    <span class="block text-blue-300 uppercase tracking-wider font-semibold mb-1">Hubungan Pengurus</span>
                    <span class="block text-white font-medium">@darulistiqomahh - Telegram</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
