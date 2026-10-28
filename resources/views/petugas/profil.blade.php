@extends('layouts.petugas')

@section('title', 'Profil Petugas & Keamanan')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Kartu Informasi Akun -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-800">Profil Petugas Lapangan</h2>
            <p class="text-xs text-slate-500">Informasi identitas akun resmi penertiban armada.</p>
        </div>

        <div class="p-6 space-y-4 text-xs">
            <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-xl shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">{{ $user->name }}</h3>
                    <p class="font-mono text-blue-700 font-semibold text-xs mt-0.5">{{ '@' . $user->username }}</p>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                        {{ $user->role_label }}
                    </span>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-slate-400 font-medium">Status Akun</dt>
                    <dd class="mt-0.5 font-bold text-emerald-700">
                        {{ $user->status_akun_label }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Tanggal Registrasi</dt>
                    <dd class="mt-0.5 font-semibold text-slate-700">
                        {{ $user->created_at->isoFormat('D MMMM Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Email Terdaftar</dt>
                    <dd class="mt-0.5 font-mono text-slate-700 truncate" title="{{ $user->email }}">
                        {{ $user->email }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-400 font-medium">Login Terakhir</dt>
                    <dd class="mt-0.5 text-slate-700">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Sesi ini' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Form Ubah Password -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-800">Perbarui Kata Sandi</h2>
            <p class="text-xs text-slate-500">Gunakan kombinasi kata sandi yang kuat untuk menjaga keamanan akun pelaporan Anda.</p>
        </div>

        @if ($errors->any())
            <div class="m-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('petugas.profil.password') }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-xs font-semibold text-slate-700 mb-1">
                    Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="current_password" id="current_password" required
                       placeholder="Masukkan kata sandi lama Anda"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" id="password" required minlength="8"
                       placeholder="Minimal 8 karakter"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                    Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                       placeholder="Ulangi kata sandi baru"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="pt-3 border-t border-slate-200 flex items-center justify-end">
                <button type="submit" class="px-5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                    Simpan Kata Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
