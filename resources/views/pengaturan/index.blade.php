@extends('layouts.admin')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun & Keamanan')
@section('breadcrumb')
    <span>Pengaturan</span> / <span class="text-slate-600 font-medium">Profil & Sandi</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form Profil -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-bold text-slate-800">Profil Petugas</h3>
                <p class="text-xs text-slate-500">Informasi nama dan alamat email resmi kedinasan.</p>
            </div>
            <form action="{{ route('pengaturan.profil') }}" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran Akses Sistem</label>
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-blue-100 text-blue-800">
                        {{ $user->role_label }}
                    </span>
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Kedinasan</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <span class="text-slate-400 block text-[11px]">Waktu Login Terakhir:</span>
                    <span class="text-slate-600 text-xs font-medium">{{ $user->last_login_at ? $user->last_login_at->isoFormat('D MMMM Y, HH:mm') . ' WIB' : '-' }}</span>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                        Perbarui Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Ubah Password -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-sm font-bold text-slate-800">Ubah Kata Sandi</h3>
                <p class="text-xs text-slate-500">Amankan akun Anda dengan sandi kombinasi huruf dan angka.</p>
            </div>
            <form action="{{ route('pengaturan.password') }}" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="password_lama" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="password_lama" id="password_lama" required placeholder="••••••••"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="password_baru" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password_baru" id="password_baru" required placeholder="Minimal 8 karakter"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="password_baru_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="password_baru_confirmation" id="password_baru_confirmation" required placeholder="••••••••"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                        Ganti Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Security Information Box -->
    <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-5 text-xs text-blue-900 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="space-y-1">
            <p class="font-bold">Protokol Keamanan & Perlindungan Data Internal</p>
            <p class="text-blue-800 leading-relaxed">
                Seluruh riwayat pengubahan data, verifikasi berkas penyitaan, serta cetak dokumen diawasi dan tercatat secara permanen di audit log sistem. Jangan memberikan kredensial login akun Anda kepada pihak yang tidak memiliki kewenangan kedinasan.
            </p>
        </div>
    </div>
</div>
@endsection
