@extends('layouts.admin')

@section('title', 'Tambah Akun Admin')
@section('page-title', 'Tambah Akun Admin Baru')
@section('breadcrumb')
    <span>Administrasi</span> / <a href="{{ route('admin.index') }}" class="hover:underline">Admin</a> / <span class="text-slate-600 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Registrasi Akun Petugas Internal</h2>
                <p class="text-xs text-slate-500">Buat kredensial akses baru khusus petugas Dinas Perhubungan Kota Bogor.</p>
            </div>
            <a href="{{ route('admin.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl">
                &larr; Batal
            </a>
        </div>

        <form action="{{ route('admin.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Lengkap Petugas <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Fazal Rachman, S.E."
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                    Alamat Email Kedinasan <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@dishub-bogor.go.id"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter (huruf & angka)"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold text-slate-700 mb-1">
                        Hak Akses (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Pengelola Data & Berkas)</option>
                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Admin (Akses Penuh & Manajemen Pengguna)</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator (Entri & Penginput Lapangan)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    Simpan Akun Petugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
