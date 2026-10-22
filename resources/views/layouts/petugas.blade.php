<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Petugas') — Dishub Kota Bogor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-inter text-slate-800 antialiased min-h-screen flex flex-col">

    {{-- TOPBAR KHUSUS PETUGAS --}}
    <header class="bg-[#1a2744] text-white shadow-md sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-kota-bogor.png') }}" alt="Logo Pemkot Bogor" class="w-8 h-8 object-contain">
                    <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub" class="w-8 h-8 object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-sm sm:text-base font-bold leading-tight">Dinas Perhubungan Kota Bogor</h1>
                        <span class="px-2 py-0.5 bg-gradient-to-r from-blue-500 to-amber-500 text-white text-[10px] font-extrabold tracking-wider rounded-md uppercase">
                            Salawang
                        </span>
                    </div>
                    <p class="text-[11px] text-blue-200/80">Sistem Pelaporan & Penertiban Lapangan</p>
                </div>
            </div>

            <!-- Petugas Info & Logout -->
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold leading-tight">{{ auth()->user()->name }}</p>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-blue-500/30 text-blue-200">
                        {{ '@' . auth()->user()->username }} • Petugas
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-rose-600/80 hover:bg-rose-600 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 cursor-pointer" title="Keluar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Navigasi Tab Menu Petugas --}}
        <div class="bg-[#121c32] border-t border-white/10 px-4 sm:px-6">
            <div class="max-w-6xl mx-auto flex items-center gap-1 sm:gap-2 overflow-x-auto py-1">
                <a href="{{ route('petugas.input') }}"
                   class="px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ request()->routeIs('petugas.input') ? 'bg-blue-600 text-white shadow-xs' : 'text-blue-200/80 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Formulir Pelaporan Kendaraan
                </a>
                <a href="{{ route('petugas.riwayat') }}"
                   class="px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ request()->routeIs('petugas.riwayat') ? 'bg-blue-600 text-white shadow-xs' : 'text-blue-200/80 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Laporan Saya
                </a>
                <a href="{{ route('petugas.profil') }}"
                   class="px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ request()->routeIs('petugas.profil') ? 'bg-blue-600 text-white shadow-xs' : 'text-blue-200/80 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil / Ubah Password
                </a>
            </div>
        </div>
    </header>

    {{-- Flash Notifications --}}
    <div class="max-w-6xl mx-auto w-full px-4 sm:px-6 pt-4">
        @if(session('success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center gap-2 mb-4 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs flex items-center gap-2 mb-4 shadow-xs">
                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif
    </div>

    {{-- MAIN BODY --}}
    <main class="max-w-6xl mx-auto w-full px-4 sm:px-6 py-4 flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-500 gap-2">
            <p>&copy; {{ date('Y') }} Dinas Perhubungan Kota Bogor. Hak Cipta Dilindungi.</p>
            <p class="text-[11px] text-slate-400">Portal Petugas Pelapor Lapangan • Salawang</p>
        </div>
    </footer>
</body>
</html>
