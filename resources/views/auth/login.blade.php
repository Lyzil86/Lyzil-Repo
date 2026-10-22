<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Selamat Datang — SIM Penertiban Angkutan Dishub Kota Bogor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-inter min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="w-full max-w-md">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <!-- Header Institusi -->
            <div class="bg-[#1a2744] text-white p-6 sm:p-8 text-center relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -top-10 w-40 h-40 bg-blue-400/10 rounded-full blur-2xl"></div>

                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-3 p-1 bg-white/10 shadow-lg">
                        <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub" class="w-14 h-14 object-contain">
                    </div>
                    <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-300 text-xs font-semibold rounded-full uppercase tracking-wider mb-2">
                        System Informasi Pengawasan Angkutan
                    </span>
                    <h2 class="text-xl font-bold tracking-tight">DINAS PERHUBUNGAN</h2>
                    <p class="text-sm text-slate-300 font-medium">Pemerintah Daerah Kota Bogor</p>
                    <p class="text-xs text-slate-400 mt-1">Pendataan Penertiban Angkutan Umum Usia > 20 Tahun</p>
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
                            Email Kedinasan atau Username
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </span>
                            <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition placeholder-slate-400"
                                placeholder="email@dishub-bogor.go.id atau username">
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
