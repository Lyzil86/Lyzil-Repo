<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Penertiban Angkutan Umum Kota Bogor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 font-inter text-gray-800 antialiased">
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    {{-- Overlay Mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen=false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden" style="display:none;"></div>

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-[#1a2744] text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 p-1">
                <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub" class="w-8 h-8 object-contain">
            </div>
            <div>
                <p class="text-xs font-semibold text-blue-300 leading-tight">Dishub Kota Bogor</p>
                <p class="text-base font-black leading-tight tracking-wider"><span class="text-amber-400">SIWA</span><span class="text-white">SANG</span></p>
                <p class="text-[10px] text-blue-200/70">Sistem Pengawasan Informasi Angkutan</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            @php
                if (auth()->user()?->isPetugas()) {
                    $navItems = [
                        ['route' => 'kendaraan.index', 'icon' => 'truck', 'label' => 'Database Kendaraan'],
                        ['route' => 'petugas.input', 'icon' => 'document', 'label' => 'Formulir Laporan'],
                        ['route' => 'petugas.riwayat', 'icon' => 'archive', 'label' => 'Riwayat Laporan'],
                        ['route' => 'petugas.profil', 'icon' => 'settings', 'label' => 'Profil Petugas'],
                    ];
                } else {
                    $navItems = [
                        ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                        ['route' => 'kendaraan.index', 'icon' => 'truck', 'label' => 'Database Kendaraan'],
                        ['route' => 'penertiban.index', 'icon' => 'document', 'label' => 'Data Penertiban'],
                        ['route' => 'barang-bukti.index', 'icon' => 'archive', 'label' => 'Barang Bukti'],
                        ['route' => 'laporan.index', 'icon' => 'chart', 'label' => 'Laporan'],
                        ['route' => 'audit-log.index', 'icon' => 'log', 'label' => 'Audit Log'],
                    ];
                }
            @endphp

            @foreach ($navItems as $item)
                @php $isActive = request()->routeIs($item['route']) || (str_ends_with($item['route'], '.index') && request()->routeIs(str_replace('.index', '.*', $item['route']))); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-all duration-150 text-sm font-medium group {{ $isActive ? 'bg-blue-600 text-white' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">
                    @include('components.nav-icon', ['icon' => $item['icon'], 'active' => $isActive])
                    {{ $item['label'] }}
                </a>
            @endforeach

            @if(auth()->user()?->isSuperadmin())
            <div class="mt-4 mb-1 px-3">
                <p class="text-xs font-semibold text-blue-300/60 uppercase tracking-wider">Administrasi</p>
            </div>
            <a href="{{ route('admin.petugas.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg mb-1 transition-all duration-150 text-sm font-medium {{ request()->routeIs('admin.petugas.*') ? 'bg-blue-600 text-white' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    @include('components.nav-icon', ['icon' => 'users', 'active' => request()->routeIs('admin.petugas.*')])
                    <span>Akun Petugas</span>
                </div>
                @php
                    $pendingCount = \App\Models\User::where('role', 'petugas')->where('status_akun', 'menunggu_persetujuan')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-amber-950">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-all duration-150 text-sm font-medium {{ request()->routeIs('admin.index', 'admin.create', 'admin.edit') ? 'bg-blue-600 text-white' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">
                @include('components.nav-icon', ['icon' => 'settings', 'active' => request()->routeIs('admin.index', 'admin.create', 'admin.edit')])
                Manajemen Admin
            </a>
            @endif

            <div class="mt-4 mb-1 px-3">
                <p class="text-xs font-semibold text-blue-300/60 uppercase tracking-wider">Akun</p>
            </div>
            <a href="{{ route('pengaturan') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-all duration-150 text-sm font-medium {{ request()->routeIs('pengaturan*') ? 'bg-blue-600 text-white' : 'text-blue-100/80 hover:bg-white/10 hover:text-white' }}">
                @include('components.nav-icon', ['icon' => 'settings', 'active' => false])
                Pengaturan
            </a>
        </nav>

        {{-- User info + logout --}}
        <div class="border-t border-white/10 px-4 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-xs text-blue-300/70 truncate">{{ auth()->user()?->role_label }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-300 hover:bg-red-500/20 hover:text-red-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-gray-200 px-4 lg:px-6 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-base lg:text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                    <div class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</div>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Identitas Visual Kota Bogor & SIWASANG --}}
                <div class="flex items-center gap-2 sm:gap-3 bg-slate-50 border border-slate-200/80 rounded-xl px-2.5 py-1.5 shadow-sm">
                    <!-- 1. Logo Pemkot Bogor -->
                    <div class="flex items-center gap-1" title="Pemerintah Kota Bogor">
                        <img src="{{ asset('images/logo-kota-bogor.png') }}" alt="Logo Pemkot Bogor" class="w-6 h-6 object-contain">
                    </div>

                    <!-- 2. Logo Dishub Kota Bogor -->
                    <div class="flex items-center gap-1" title="Dinas Perhubungan Kota Bogor">
                        <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub Kota Bogor" class="w-6 h-6 object-contain">
                    </div>

                    <div class="h-4 w-px bg-slate-300 hidden sm:block"></div>

                    <!-- 3. Maskot Rubo -->
                    <div class="hidden sm:flex items-center gap-1" title="Maskot RuBo (Rusa Bogor)">
                        <img src="{{ asset('images/maskot-rubo.png') }}" alt="Maskot RuBo" class="w-6 h-6 object-contain">
                        <span class="text-[10px] font-bold text-blue-900 hidden md:inline">RuBo</span>
                    </div>

                    <!-- 4. Tugu Kujang -->
                    <div class="hidden md:flex items-center gap-1" title="Landmark Tugu Kujang">
                        <div class="w-6 h-6 rounded-full overflow-hidden border border-amber-300 shadow-xs flex-shrink-0">
                            <img src="{{ asset('images/tugu-kujang.jpg') }}" alt="Tugu Kujang" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[10px] font-medium text-slate-600 hidden lg:inline">Tugu Kujang</span>
                    </div>

                    <!-- 5. Lawang Salapan -->
                    <div class="hidden md:flex items-center gap-1" title="Tepas Lawang Salapan">
                        <div class="w-6 h-6 rounded-full overflow-hidden border border-blue-300 shadow-xs flex-shrink-0">
                            <img src="{{ asset('images/lawang-salapan.jpg') }}" alt="Lawang Salapan" class="w-full h-full object-cover">
                        </div>
                        <span class="text-[10px] font-medium text-slate-600 hidden lg:inline">Lawang Salapan</span>
                    </div>

                    <div class="h-4 w-px bg-slate-300"></div>

                    <!-- 6. Teks Identitas SIWASANG -->
                    <div class="flex items-center gap-1">
                        <span class="px-2 py-0.5 bg-gradient-to-r from-blue-700 via-indigo-700 to-amber-600 text-white text-[11px] font-extrabold tracking-wider rounded-lg shadow-xs uppercase">
                            <span class="text-amber-400">SIWA</span><span class="text-white">SANG</span>
                        </span>
                    </div>
                </div>

                <span class="hidden sm:block text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full whitespace-nowrap">
                    {{ now()->isoFormat('dddd, D MMMM Y') }}
                </span>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || $errors->any())
        <div class="px-4 lg:px-6 pt-4" x-data="{show: true}" x-show="show" x-transition>
            @if(session('success'))
            <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-2">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <p class="text-sm">{{ session('success') }}</p>
                <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-2">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <p class="text-sm">{{ session('error') }}</p>
                <button @click="show=false" class="ml-auto text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
            @endif
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-2">
                <p class="text-sm font-semibold mb-1">Terdapat kesalahan input:</p>
                <ul class="text-sm list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @endif

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto px-4 lg:px-6 py-4">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
