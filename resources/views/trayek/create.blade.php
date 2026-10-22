@extends('layouts.admin')

@section('title', 'Tambah Trayek')
@section('page-title', 'Tambah Trayek Angkutan')
@section('breadcrumb')
    <span>Master Data</span> / <a href="{{ route('trayek.index') }}" class="hover:underline">Trayek</a> / <span class="text-slate-600 font-medium">Tambah</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Master Data Trayek Angkutan</h2>
                <p class="text-xs text-slate-500">Tambahkan informasi nomor kode trayek dan rute operasional.</p>
            </div>
            <a href="{{ route('trayek.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl">
                &larr; Batal
            </a>
        </div>

        <form action="{{ route('trayek.store') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kode_trayek" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kode Trayek <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="kode_trayek" id="kode_trayek" value="{{ old('kode_trayek') }}" required
                           placeholder="Contoh: 01, 02, 03A"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono font-bold">
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="diarsipkan" {{ old('status') === 'diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="nama_trayek" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama / Keterangan Trayek <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_trayek" id="nama_trayek" value="{{ old('nama_trayek') }}" required
                       placeholder="Contoh: Baranangsiang - Bubulak via Otista"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="asal" class="block text-xs font-semibold text-slate-700 mb-1">Titik Asal / Terminal</label>
                    <input type="text" name="asal" id="asal" value="{{ old('asal') }}" placeholder="Contoh: Terminal Baranangsiang"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="tujuan" class="block text-xs font-semibold text-slate-700 mb-1">Titik Tujuan / Terminal</label>
                    <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}" placeholder="Contoh: Terminal Bubulak"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Lintasan / Rute Detail</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Jalan-jalan yang dilintasi trayek..."
                          class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('trayek.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    Simpan Trayek
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
