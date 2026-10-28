@extends('layouts.admin')

@section('title', 'Detail Barang Bukti: ' . $barangBukti->jenis_label)
@section('page-title', 'Detail Barang Bukti')
@section('breadcrumb')
    <span>Operasional</span> / <a href="{{ route('barang-bukti.index') }}" class="hover:underline">Barang Bukti</a> / <span class="text-slate-600 font-medium">Detail</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ imageModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-bold uppercase {{ $barangBukti->status === 'disita' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ ucfirst($barangBukti->status) }}
                </span>
                <span class="text-xs text-slate-400 font-mono">ID: #{{ $barangBukti->id }}</span>
            </div>
            <h2 class="text-xl font-bold text-slate-800">{{ $barangBukti->jenis_label }}</h2>
            <p class="text-xs text-slate-500">
                Dokumen: <strong class="text-slate-800 font-mono">{{ $barangBukti->nomor_dokumen ?: '-' }}</strong>
                • Berkas Surat: <a href="{{ route('penertiban.show', $barangBukti->penertib) }}" class="text-blue-600 hover:underline font-semibold">{{ $barangBukti->penertib?->nomor_surat ?: 'Surat #' . $barangBukti->penertib_id }}</a>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penertiban.show', $barangBukti->penertib_id) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                &larr; Berkas Penertiban
            </a>
            <a href="{{ route('barang-bukti.edit', $barangBukti) }}" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Barang Bukti
            </a>
        </div>
    </div>

    <!-- Konten Utama 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Foto Bukti Fisik -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-5">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Foto Barang Bukti</h3>

                @if($barangBukti->foto)
                    <div class="space-y-3">
                        <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-100 aspect-video sm:aspect-square flex items-center justify-center">
                            <img src="{{ $barangBukti->foto_url }}" alt="Foto Barang Bukti" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button type="button" @click="imageModal = true" class="px-3 py-1.5 bg-white text-slate-800 rounded-lg text-xs font-semibold shadow cursor-pointer">
                                    🔍 Perbesar Foto
                                </button>
                                <a href="{{ $barangBukti->foto_url }}" target="_blank" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold shadow">
                                    Buka Asli &nearr;
                                </a>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 text-center">Klik pada gambar atau tombol untuk melihat ukuran penuh.</p>
                    </div>
                @else
                    <div class="rounded-xl border-2 border-dashed border-slate-200 p-8 text-center bg-slate-50">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs font-semibold text-slate-600">Belum ada foto barang bukti</p>
                        <p class="text-[11px] text-slate-400 mt-1 mb-3">Foto dapat diunggah sebagai arsip dokumentasi fisik.</p>
                        <a href="{{ route('barang-bukti.edit', $barangBukti) }}" class="inline-block px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition">
                            + Unggah Foto Sekarang
                        </a>
                    </div>
                @endif
            </div>

            <!-- Box Status & Kondisi -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-xs space-y-3">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status & Penguasaan</h3>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Kondisi Fisik:</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $barangBukti->kondisi }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Status Penahanan:</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $barangBukti->status }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-slate-500">Tgl Dicatat:</span>
                    <span class="font-mono text-slate-700">{{ $barangBukti->created_at ? $barangBukti->created_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Lengkap Barang Bukti & Berkas Terkait -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Spesifikasi Bukti -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-blue-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                    Spesifikasi Barang Bukti
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Jenis Barang Bukti</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $barangBukti->jenis_label }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Nomor Dokumen / Plat Fisik</span>
                        <span class="font-mono font-bold text-slate-900 text-sm">{{ $barangBukti->nomor_dokumen ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Tanggal Penerbitan Dokumen</span>
                        <span class="font-semibold text-slate-800">{{ $barangBukti->tanggal_dokumen ? $barangBukti->tanggal_dokumen->isoFormat('D MMMM Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Masa Berlaku / Kadaluarsa</span>
                        <span class="font-semibold {{ $barangBukti->tanggal_kadaluarsa && $barangBukti->tanggal_kadaluarsa->isPast() ? 'text-rose-600 font-bold' : 'text-slate-800' }}">
                            {{ $barangBukti->tanggal_kadaluarsa ? $barangBukti->tanggal_kadaluarsa->isoFormat('D MMMM Y') : '-' }}
                            @if($barangBukti->tanggal_kadaluarsa && $barangBukti->tanggal_kadaluarsa->isPast())
                                <span class="text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded ml-1 font-semibold">Kadaluarsa</span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Tanggal Pengembalian (Bila ada)</span>
                        <span class="font-semibold text-slate-800">{{ $barangBukti->tanggal_pengembalian ? $barangBukti->tanggal_pengembalian->isoFormat('D MMMM Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Kondisi Fisik Saat Disita</span>
                        <span class="font-semibold text-slate-800 capitalize">{{ $barangBukti->kondisi }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <span class="text-slate-400 block text-xs mb-1">Keterangan Catatan Tambahan:</span>
                    <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed font-mono">
                        {{ $barangBukti->keterangan ?: 'Tidak ada keterangan catatan tambahan.' }}
                    </p>
                </div>
            </div>

            <!-- Berkas Penertiban & Kendaraan Terkait -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-blue-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                    Keterkaitan Berkas Penindakan & Objek Kendaraan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                        <span class="text-blue-900 font-bold block uppercase text-[11px]">Berkas Surat Penyitaan</span>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Nomor Surat:</span>
                            <a href="{{ route('penertiban.show', $barangBukti->penertib) }}" class="font-bold text-blue-700 hover:underline">
                                {{ $barangBukti->penertib?->nomor_surat ?: 'Surat #' . $barangBukti->penertib_id }}
                            </a>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Tanggal Penertiban:</span>
                            <span class="font-semibold text-slate-800">{{ $barangBukti->penertib?->tanggal_penertiban ? $barangBukti->penertib->tanggal_penertiban->isoFormat('D MMMM Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Lokasi Penindakan:</span>
                            <span class="font-semibold text-slate-800">{{ $barangBukti->penertib?->lokasi_penertiban ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Petugas PPNS:</span>
                            <span class="font-semibold text-slate-800">{{ $barangBukti->penertib?->petugas_penertiban ?: 'PPNS Dishub Kota Bogor' }}</span>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                        <span class="text-blue-900 font-bold block uppercase text-[11px]">Objek Kendaraan Terdata</span>
                        @php $kendaraan = $barangBukti->penertib?->kendaraan; @endphp
                        @if($kendaraan)
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nomor Kendaraan:</span>
                                <a href="{{ route('kendaraan.show', $kendaraan) }}" class="font-bold text-blue-700 hover:underline text-sm">
                                    {{ $kendaraan->nomor_kendaraan }}
                                </a>
                                @if($kendaraan->trashed())
                                    <span class="text-[10px] text-rose-500 font-semibold">(Dihapus)</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Tahun / Usia Operasional:</span>
                                <span class="font-semibold text-slate-800">Tahun {{ $kendaraan->tahun_kendaraan }} ({{ $kendaraan->usia_operasional }} Tahun)</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Jenis / Trayek:</span>
                                <span class="font-semibold text-slate-800">
                                    {{ $kendaraan->jenis_kendaraan }}
                                    @if($kendaraan->trayek)
                                        / Trayek {{ $kendaraan->trayek->kode_trayek }}
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Pemilik / Pengemudi:</span>
                                <span class="font-semibold text-slate-800">{{ $kendaraan->pemilikPengemudis->first()?->nama ?: '-' }}</span>
                            </div>
                        @else
                            <p class="text-slate-400 italic">Data kendaraan objek tidak ditemukan atau telah dihapus permanen.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lightbox Pembesaran Foto -->
    @if($barangBukti->foto)
        <div x-show="imageModal" style="display:none;"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="imageModal = false" class="relative max-w-4xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl">
                <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-bold">{{ $barangBukti->jenis_label }} - {{ $barangBukti->nomor_dokumen ?: 'Dokumen Bukti' }}</h4>
                        <p class="text-xs text-slate-400">Berkas {{ $barangBukti->penertib?->nomor_surat }}</p>
                    </div>
                    <button type="button" @click="imageModal = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 bg-black/5 flex items-center justify-center max-h-[75vh] overflow-auto">
                    <img src="{{ $barangBukti->foto_url }}" alt="Foto Barang Bukti Penuh" class="max-h-[70vh] w-auto object-contain rounded-lg shadow">
                </div>
                <div class="p-3 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs text-slate-500">
                    <span>Arsip Barang Bukti Dinas Perhubungan Kota Bogor</span>
                    <a href="{{ $barangBukti->foto_url }}" download class="px-3 py-1 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Unduh Foto
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
