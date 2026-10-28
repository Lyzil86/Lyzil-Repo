@extends('layouts.admin')

@section('title', 'Edit Barang Bukti')
@section('page-title', 'Edit Data Barang Bukti')
@section('breadcrumb')
    <span>Operasional</span> / <a href="{{ route('barang-bukti.index') }}" class="hover:underline">Barang Bukti</a> / <span class="text-slate-600 font-medium">Edit</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Edit Barang Bukti: {{ $barangBukti->jenis_label }}</h2>
                <p class="text-xs text-slate-500">
                    Surat: <strong class="text-slate-800">{{ $barangBukti->penertib?->nomor_surat ?: 'Berkas #' . $barangBukti->penertib_id }}</strong>
                    • Kendaraan: {{ $barangBukti->penertib?->kendaraan?->nomor_kendaraan ?? '-' }}
                </p>
            </div>
            <a href="{{ route('penertiban.show', $barangBukti->penertib_id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl">
                &larr; Batal
            </a>
        </div>

        <form action="{{ route('barang-bukti.update', $barangBukti) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jenis" class="block text-xs font-semibold text-slate-700 mb-1">
                        Jenis Barang Bukti <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis" id="jenis" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-bold">
                        <option value="kir" {{ old('jenis', $barangBukti->jenis) === 'kir' ? 'selected' : '' }}>1. Buku Uji Berkala (KIR)</option>
                        <option value="kps" {{ old('jenis', $barangBukti->jenis) === 'kps' ? 'selected' : '' }}>2. Kartu Pengawasan (KPS)</option>
                        <option value="stnk" {{ old('jenis', $barangBukti->jenis) === 'stnk' ? 'selected' : '' }}>3. Surat Tanda Kendaraan Bermotor (STNK)</option>
                        <option value="kendaraan" {{ old('jenis', $barangBukti->jenis) === 'kendaraan' ? 'selected' : '' }}>4. Kendaraan / Unit Fisik</option>
                        <option value="lainnya" {{ old('jenis', $barangBukti->jenis) === 'lainnya' ? 'selected' : '' }}>5. Dokumen / Barang Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="nomor_dokumen" class="block text-xs font-semibold text-slate-700 mb-1">
                        Nomor Dokumen / Plat Unit
                    </label>
                    <input type="text" name="nomor_dokumen" id="nomor_dokumen"
                           value="{{ old('nomor_dokumen', $barangBukti->nomor_dokumen) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kondisi" class="block text-xs font-semibold text-slate-700 mb-1">
                        Kondisi Fisik <span class="text-rose-500">*</span>
                    </label>
                    <select name="kondisi" id="kondisi" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                        <option value="baik" {{ old('kondisi', $barangBukti->kondisi) === 'baik' ? 'selected' : '' }}>Baik / Utuh</option>
                        <option value="rusak" {{ old('kondisi', $barangBukti->kondisi) === 'rusak' ? 'selected' : '' }}>Rusak / Sobek</option>
                        <option value="tidak_ada" {{ old('kondisi', $barangBukti->kondisi) === 'tidak_ada' ? 'selected' : '' }}>Tidak Memiliki / Nihil</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                        Status Penguasaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                        <option value="disita" {{ old('status', $barangBukti->status) === 'disita' ? 'selected' : '' }}>Disita di Kantor Dishub</option>
                        <option value="dikembalikan" {{ old('status', $barangBukti->status) === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="dimusnahkan" {{ old('status', $barangBukti->status) === 'dimusnahkan' ? 'selected' : '' }}>Dimusnahkan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal_dokumen" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Dokumen</label>
                    <input type="date" name="tanggal_dokumen" id="tanggal_dokumen"
                           value="{{ old('tanggal_dokumen', $barangBukti->tanggal_dokumen ? $barangBukti->tanggal_dokumen->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="tanggal_kadaluarsa" class="block text-xs font-semibold text-slate-700 mb-1">Kadaluarsa</label>
                    <input type="date" name="tanggal_kadaluarsa" id="tanggal_kadaluarsa"
                           value="{{ old('tanggal_kadaluarsa', $barangBukti->tanggal_kadaluarsa ? $barangBukti->tanggal_kadaluarsa->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>

                <div>
                    <label for="tanggal_pengembalian" class="block text-xs font-semibold text-slate-700 mb-1">Tgl Pengembalian</label>
                    <input type="date" name="tanggal_pengembalian" id="tanggal_pengembalian"
                           value="{{ old('tanggal_pengembalian', $barangBukti->tanggal_pengembalian ? $barangBukti->tanggal_pengembalian->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Catatan Bukti</label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('keterangan', $barangBukti->keterangan) }}</textarea>
            </div>

            <!-- Upload Foto Barang Bukti (Opsional) -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                <label class="block text-xs font-semibold text-slate-700">
                    Foto Barang Bukti <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>

                @if($barangBukti->foto)
                    <div class="flex items-start gap-4 p-3 bg-white rounded-lg border border-slate-200">
                        <img src="{{ $barangBukti->foto_url }}" alt="Foto Barang Bukti" class="w-24 h-24 object-cover rounded-lg border border-slate-200 shadow-sm">
                        <div class="space-y-2">
                            <p class="text-xs text-slate-600 font-medium">Foto saat ini tersimpan.</p>
                            <label class="inline-flex items-center gap-2 text-xs text-rose-600 cursor-pointer">
                                <input type="checkbox" name="hapus_foto" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span>Hapus foto ini saat menyimpan</span>
                            </label>
                        </div>
                    </div>
                @endif

                <div>
                    <input type="file" name="foto" id="foto" accept="image/*"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                    <p class="text-[11px] text-slate-400 mt-1">Format didukung: JPG, PNG, WEBP (Maks. 5 MB). Biarkan kosong jika tidak ingin mengubah/menambahkan gambar.</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('penertiban.show', $barangBukti->penertib_id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
