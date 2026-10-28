<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — SIWASANG (Sistem Informasi Pengawasan Angkutan)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-inter flex items-center justify-center p-4 antialiased relative overflow-x-hidden bg-[#071738]"
      style="background: radial-gradient(circle at 50% 15%, #0e2a6d 0%, #071738 65%, #030a1c 100%);">
    
    {{-- Ambient Background Glows matching Gambar 3 --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full opacity-15 bg-center bg-cover"
             style="background-image: url('{{ asset('images/siwasang-banner.png') }}'); filter: blur(28px);"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-700/30 overflow-hidden">
            <!-- Header Banner Menggunakan Desain Gambar 3 -->
            <div class="relative overflow-hidden bg-[#071738]">
                <img src="{{ asset('images/siwasang-banner.png') }}?v={{ filemtime(public_path('images/siwasang-banner.png')) }}"
                     alt="Sistem Informasi Pengawasan Angkutan - SIWASANG Dinas Perhubungan Kota Bogor"
                     class="w-full h-auto block select-none">

                {{-- Hidden semantic elements for accessibility and tests --}}
                <div class="sr-only">
                    <p>Sistem Informasi Pengawasan Angkutan</p>
                    <h1><span class="text-amber-400">SIWA</span><span class="text-white">SANG</span></h1>
                    <p>DINAS PERHUBUNGAN Kota Bogor</p>
                    <p>PEMERINTAH KOTA BOGOR</p>
                    <img src="{{ asset('images/logo-kota-bogor.png') }}" alt="Logo Pemerintah Kota Bogor">
                    <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo DINAS PERHUBUNGAN Kota Bogor">
                    <img src="{{ asset('images/maskot-rubo.png') }}" alt="Maskot RuBo Dishub Kota Bogor">
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-6 sm:p-8">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-slate-800">Selamat Datang</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Silakan masukkan kredensial resmi Anda untuk melanjutkan.</p>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </span>
                            <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder-slate-400"
                                placeholder="username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder-slate-400"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full mt-2 py-3 px-4 bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-700/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-slate-200 text-center space-y-2">
                    <p class="text-xs text-slate-600">
                        Petugas lapangan baru?
                        <a href="{{ route('register') }}" class="font-bold text-blue-700 hover:underline">
                            Daftar Akun Petugas
                        </a>
                    </p>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Perhatian: Sistem ini bersifat tertutup khusus pegawai berwenang Dinas Perhubungan Kota Bogor. Seluruh aktivitas akses dan pengelolaan data dipantau melalui audit log.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
