@extends('layouts.admin')

@section('title', 'Pencatatan Barang Bukti')
@section('page-title', 'Form Pencatatan Barang Bukti')
@section('breadcrumb')
    <span>Operasional</span> / <a href="{{ route('barang-bukti.index') }}" class="hover:underline">Barang Bukti</a> / <span class="text-slate-600 font-medium">Tambah Baru</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Catat Barang Bukti Penertiban</h2>
                <p class="text-xs text-slate-500">KIR, KPS, STNK, atau unit fisik kendaraan yang disita dari lapangan.</p>
            </div>
            <a href="{{ $penertib ? route('penertiban.show', $penertib) : route('barang-bukti.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl">
                &larr; Batal
            </a>
        </div>

        <form action="{{ route('barang-bukti.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div>
                <label for="penertib_id" class="block text-xs font-semibold text-slate-700 mb-1">
                    Hubungkan ke Berkas Surat Penyitaan <span class="text-rose-500">*</span>
                </label>
                <select name="penertib_id" id="penertib_id" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                    <option value="">-- Pilih Surat Penertiban --</option>
                    @foreach($penertibs as $p)
                        <option value="{{ $p->id }}" {{ (old('penertib_id', $penertib?->id) == $p->id) ? 'selected' : '' }}>
                            {{ $p->nomor_surat ?: 'Surat #' . $p->id }} (Kendaraan: {{ $p->kendaraan->nomor_kendaraan }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jenis" class="block text-xs font-semibold text-slate-700 mb-1">
                        Jenis Barang Bukti <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis" id="jenis" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-bold">
                        <option value="kir" {{ old('jenis') === 'kir' ? 'selected' : '' }}>1. Buku Uji Berkala (KIR)</option>
                        <option value="kps" {{ old('jenis') === 'kps' ? 'selected' : '' }}>2. Kartu Pengawasan (KPS)</option>
                        <option value="stnk" {{ old('jenis') === 'stnk' ? 'selected' : '' }}>3. Surat Tanda Kendaraan Bermotor (STNK)</option>
                        <option value="kendaraan" {{ old('jenis') === 'kendaraan' ? 'selected' : '' }}>4. Kendaraan / Unit Fisik</option>
                        <option value="lainnya" {{ old('jenis') === 'lainnya' ? 'selected' : '' }}>5. Dokumen / Barang Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="nomor_dokumen" class="block text-xs font-semibold text-slate-700 mb-1">
                        Nomor Dokumen / Plat Unit
                    </label>
                    <input type="text" name="nomor_dokumen" id="nomor_dokumen" value="{{ old('nomor_dokumen') }}"
                           placeholder="Contoh: BGR-2023-8911"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kondisi" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kondisi Fisik <span class="text-rose-500">*</span>
                    </label>
                    <select name="kondisi" id="kondisi" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                        <option value="baik" {{ old('kondisi', 'baik') === 'baik' ? 'selected' : '' }}>Baik / Utuh</option>
                        <option value="rusak" {{ old('kondisi') === 'rusak' ? 'selected' : '' }}>Rusak / Sobek</option>
                        <option value="tidak_ada" {{ old('kondisi') === 'tidak_ada' ? 'selected' : '' }}>Tidak Memiliki / Nihil</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                        Status Penguasaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                        <option value="disita" {{ old('status', 'disita') === 'disita' ? 'selected' : '' }}>Disita di Kantor Dishub</option>
                        <option value="dikembalikan" {{ old('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="dimusnahkan" {{ old('status') === 'dimusnahkan' ? 'selected' : '' }}>Dimusnahkan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_dokumen" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Terbit Dokumen</label>
                    <input type="date" name="tanggal_dokumen" id="tanggal_dokumen" value="{{ old('tanggal_dokumen') }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="tanggal_kadaluarsa" class="block text-xs font-semibold text-slate-700 mb-1">Masa Berlaku / Kadaluarsa</label>
                    <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa') }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Catatan Bukti</label>
                <textarea name="keterangan" id="keterangan" rows="3" placeholder="Misal: Disimpan di pool Cibuluh, masa uji telah habis, dll..."
                          class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('keterangan') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ $penertib ? route('penertiban.show', $penertib) : route('barang-bukti.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    Simpan Barang Bukti
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
