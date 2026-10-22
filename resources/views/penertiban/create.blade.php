@extends('layouts.admin')

@section('title', 'Penerbitan Surat Penyitaan')
@section('page-title', 'Form Penerbitan Surat Penyitaan')
@section('breadcrumb')
    <span>Operasional</span> / <a href="{{ route('penertiban.index') }}" class="hover:underline">Penertiban</a> / <span class="text-slate-600 font-medium">Buat Baru</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Formulir Surat Penyitaan Resmi</h2>
                <p class="text-xs text-slate-500">Berdasarkan format baku Surat Penyitaan Bidang Dalops/Daltib Dishub Kota Bogor.</p>
            </div>
            <a href="{{ route('penertiban.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('penertiban.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Bagian 1: Nomor Surat & Legalitas -->
            <div>
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3 pb-1 border-b border-blue-50">
                    A. Administrasi Surat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="nomor_surat" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nomor Surat Penyitaan
                        </label>
                        <input type="text" name="nomor_surat" id="nomor_surat"
                               value="{{ old('nomor_surat', '500/' . rand(100, 999) . ' - Daltib') }}"
                               placeholder="500/ ... - Daltib"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono font-bold">
                    </div>

                    <div>
                        <label for="tanggal_surat" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tanggal Surat
                        </label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat"
                               value="{{ old('tanggal_surat', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                            Status Berkas <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif (Barang Bukti Disita)</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                            <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai (Penanganan Tuntas)</option>
                            <option value="dibatalkan" {{ old('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Kendaraan Objek Penindakan -->
            <div>
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3 pb-1 border-b border-blue-50">
                    B. Objek Kendaraan & Lokasi Penindakan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kendaraan_id" class="block text-xs font-semibold text-slate-700 mb-1">
                            Pilih Kendaraan Angkutan <span class="text-rose-500">*</span>
                        </label>
                        <select name="kendaraan_id" id="kendaraan_id" required
                                class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                            <option value="">-- Pilih Kendaraan Terdata --</option>
                            @foreach($kendaraans as $k)
                                <option value="{{ $k->id }}" {{ (old('kendaraan_id', $kendaraan?->id) == $k->id) ? 'selected' : '' }}>
                                    {{ $k->nomor_kendaraan }} ({{ $k->kode_data }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="usia_teknis_operasional" class="block text-xs font-semibold text-slate-700 mb-1">
                            Usia Teknis Operasional (Tahun)
                        </label>
                        <input type="number" name="usia_teknis_operasional" id="usia_teknis_operasional"
                               value="{{ old('usia_teknis_operasional', $kendaraan ? $kendaraan->usia_operasional : 21) }}"
                               placeholder="Contoh: 22" min="1" max="100"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-bold">
                    </div>

                    <div>
                        <label for="lokasi_penertiban" class="block text-xs font-semibold text-slate-700 mb-1">
                            Lokasi Pelanggaran / Penertiban <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi_penertiban" id="lokasi_penertiban"
                               value="{{ old('lokasi_penertiban', $kendaraan?->lokasi_pelanggaran ?? 'Jl. Raya Tajur, Kota Bogor') }}" required
                               placeholder="Titik lokasi pencegatan / razia penertiban"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>

                    <div>
                        <label for="tanggal_penertiban" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tanggal Penertiban <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_penertiban" id="tanggal_penertiban"
                               value="{{ old('tanggal_penertiban', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Pejabat PPNS & Dasar Hukum -->
            <div>
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3 pb-1 border-b border-blue-50">
                    C. Pejabat Berwenang & Konsiderans Hukum
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="petugas_penertiban" class="block text-xs font-semibold text-slate-700 mb-1">
                            Pejabat PPNS Dinas Perhubungan
                        </label>
                        <input type="text" name="petugas_penertiban" id="petugas_penertiban"
                               value="{{ old('petugas_penertiban', 'FAZAL RACHMAN SE') }}"
                               placeholder="Nama Pejabat PPNS yang bertanda tangan"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                    </div>

                    <div>
                        <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Keterangan Tambahan
                        </label>
                        <input type="text" name="keterangan" id="keterangan"
                               value="{{ old('keterangan', 'Penindakan operasi penertiban batas usia armada angkutan') }}"
                               class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>

                    <div class="md:col-span-2">
                        <label for="dasar_hukum" class="block text-xs font-semibold text-slate-700 mb-1">
                            Dasar Hukum / Konsiderans Penindakan
                        </label>
                        <textarea name="dasar_hukum" id="dasar_hukum" rows="3"
                                  class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('dasar_hukum', 'Berdasarkan Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, Perda Kota Bogor Nomor 8 Tahun 2023 tentang Transportasi dan Perwali Kota Bogor Nomor 11 Tahun 2026 tentang Rasionalisasi, Peremajaan, dan Penghapusan Kendaraan Bermotor Umum dalam Trayek. Penyitaan dilakukan karena kendaraan tersebut secara teknis dan administrasi telah melanggar batas usia operasional armada angkutan umum yang ditetapkan oleh Pemerintah Daerah Kota Bogor yaitu maksimal 20 Tahun.') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="catatan_internal" class="block text-xs font-semibold text-slate-700 mb-1">
                            Catatan Internal Petugas (Tidak Dicetak di Surat)
                        </label>
                        <textarea name="catatan_internal" id="catatan_internal" rows="2" placeholder="Catatan internal pengawasan atau koordinasi..."
                                  class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('catatan_internal') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('penertiban.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Terbitkan Surat & Lanjut ke Barang Bukti
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
