@extends('layouts.admin')

@section('title', 'Detail Kendaraan ' . $kendaraan->nomor_kendaraan)
@section('page-title', 'Detail Kendaraan Terdata')
@section('breadcrumb')
    <span>Database</span> / <a href="{{ route('kendaraan.index') }}" class="hover:underline">Kendaraan</a> / <span class="text-slate-600 font-medium">{{ $kendaraan->nomor_kendaraan }}</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ deleteModal: false, deleteUrl: '', deleteItemName: '' }">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('kendaraan.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-800">{{ $kendaraan->nomor_kendaraan }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-100 text-blue-800">
                        {{ $kendaraan->kode_data }}
                    </span>
                    @if($kendaraan->status_verifikasi === 'diverifikasi')
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                            Terverifikasi
                        </span>
                    @elseif($kendaraan->status_verifikasi === 'menunggu')
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800">
                            Ditolak
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Diregistrasi pada {{ $kendaraan->tanggal_pendataan->isoFormat('D MMMM Y') }} oleh {{ $kendaraan->createdBy?->name ?? 'Petugas' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('penertiban.create', ['kendaraan_id' => $kendaraan->id]) }}"
               class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Terbitkan Surat Penyitaan
            </a>
            <a href="{{ route('kendaraan.edit', $kendaraan) }}"
               class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            @if(!auth()->user()?->isPetugas())
                <button type="button"
                        @click="deleteModal = true; deleteUrl = '{{ route('kendaraan.destroy', $kendaraan) }}'; deleteItemName = '{{ $kendaraan->nomor_kendaraan }}'"
                        class="p-2 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-xl transition cursor-pointer" title="Hapus Kendaraan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Batas Usia 20 Tahun -->
    @php $usia = $kendaraan->usia_operasional; @endphp
    @if($usia >= 20)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3 text-amber-900">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-xs">
                <p class="font-bold">Pelanggaran Batas Usia Operasional Angkutan ({{ $usia }} Tahun)</p>
                <p class="mt-0.5 text-amber-800 leading-relaxed">
                    Armada ini telah berusia <strong>{{ $usia }} tahun</strong> (Tahun pembuatan: {{ $kendaraan->tahun_kendaraan }}). Sesuai Perda Kota Bogor No. 8/2023 & Perwali No. 11/2026, batas maksimal usia armada adalah 20 tahun. Kendaraan dinilai tidak laik operasi dan wajib ditertibkan/disita dari trayek umum.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 2 Kolom: Detail Teknis & Spesifikasi Kendaraan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                    <span>Spesifikasi & Data Teknis Kendaraan</span>
                    <span class="text-slate-400 font-normal">Sesuai Fisik & Dokumen</span>
                </h3>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
                    <div>
                        <dt class="text-slate-400 font-medium">Nomor Kendaraan / TNKB</dt>
                        <dd class="text-slate-800 font-bold text-sm mt-0.5">{{ $kendaraan->nomor_kendaraan }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Kode Registrasi Sistem</dt>
                        <dd class="text-slate-800 font-mono font-semibold mt-0.5">{{ $kendaraan->kode_data }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Jenis Angkutan</dt>
                        <dd class="text-slate-800 font-semibold mt-0.5">{{ $kendaraan->jenis_kendaraan }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Trayek Operasional</dt>
                        <dd class="text-slate-800 font-semibold mt-0.5">
                            @if($kendaraan->nama_trayek)
                                <span class="text-blue-700 font-semibold">{{ $kendaraan->nama_trayek }}</span>
                            @elseif($kendaraan->trayek)
                                <span class="text-blue-700">Trayek {{ $kendaraan->trayek->kode_trayek }}</span> - {{ $kendaraan->trayek->nama_trayek }}
                            @else
                                <span class="text-slate-400 italic">Tidak terdaftar trayek</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Tahun Pembuatan / Usia</dt>
                        <dd class="text-slate-800 font-bold mt-0.5">
                            {{ $kendaraan->tahun_kendaraan }} (Usia: {{ $usia }} Tahun)
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Warna Armada</dt>
                        <dd class="text-slate-800 font-semibold mt-0.5">{{ $kendaraan->warna ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Merek / Tipe</dt>
                        <dd class="text-slate-800 font-semibold mt-0.5">
                            {{ $kendaraan->merek ?? '-' }} {{ $kendaraan->model_kendaraan ? '/ ' . $kendaraan->model_kendaraan : '' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Nomor Mesin</dt>
                        <dd class="text-slate-800 font-mono font-semibold mt-0.5">{{ $kendaraan->no_mesin ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Nomor Rangka</dt>
                        <dd class="text-slate-800 font-mono font-semibold mt-0.5">{{ $kendaraan->no_rangka ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400 font-medium">Jenis Pelanggaran</dt>
                        <dd class="mt-0.5">
                            @if($kendaraan->jenis_pelanggaran === 'pelanggaran_teknis')
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $kendaraan->jenis_pelanggaran_label }}
                                </span>
                            @elseif($kendaraan->jenis_pelanggaran === 'pelanggaran_administrasi')
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $kendaraan->jenis_pelanggaran_label }}
                                </span>
                            @else
                                <span class="text-slate-500 font-medium">{{ $kendaraan->jenis_pelanggaran_label }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-400 font-medium">Lokasi Pelanggaran & Titik Koordinat</dt>
                        <dd class="mt-1 space-y-1.5">
                            <p class="text-slate-800 font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $kendaraan->lokasi_pelanggaran }}
                            </p>
                            @if($kendaraan->has_koordinat)
                                <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px]">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono">
                                        📍 {{ $kendaraan->koordinat_teks }}
                                    </span>
                                    <a href="{{ $kendaraan->google_maps_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium transition">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                        Buka Google Maps
                                    </a>
                                </div>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-400 font-medium">Catatan Petugas</dt>
                        <dd class="text-slate-700 mt-0.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            {{ $kendaraan->catatan ?: 'Tidak ada catatan tambahan.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Bagian Foto Bukti Laporan Petugas -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5" x-data="{ previewModal: false, activeImage: '' }">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Foto Bukti Laporan Petugas
                        </h3>
                        <p class="text-[11px] text-slate-400">Dokumentasi fisik pemeriksaan armada di lapangan</p>
                    </div>
                    @if($kendaraan->has_foto_bukti)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                            {{ count($kendaraan->foto_bukti) }} Foto Dilampirkan
                        </span>
                    @endif
                </div>

                @if($kendaraan->has_foto_bukti)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($kendaraan->foto_bukti_urls as $url)
                            <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shadow-2xs aspect-video cursor-pointer"
                                 @click="activeImage = '{{ $url }}'; previewModal = true">
                                <img src="{{ $url }}" alt="Bukti Foto" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    Perbesar
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Tidak ada foto bukti yang dilampirkan pada laporan pendataan kendaraan ini.
                    </div>
                @endif

                <!-- Modal Preview Gambar -->
                <div x-show="previewModal" style="display:none;"
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/80"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div @click.away="previewModal = false" class="relative max-w-4xl w-full bg-slate-900 rounded-2xl overflow-hidden p-2 shadow-2xl">
                        <button type="button" @click="previewModal = false" class="absolute top-4 right-4 z-10 p-2 bg-black/60 hover:bg-black/90 text-white rounded-full transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <img :src="activeImage" class="w-full max-h-[80vh] object-contain rounded-xl mx-auto">
                    </div>
                </div>
            </div>

            <!-- Bagian Pemilik & Pengemudi -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider">
                            Data Pemilik & Pengemudi Terkait
                        </h3>
                        <p class="text-[11px] text-slate-400">Data identitas pihak yang mengoperasikan atau memiliki kendaraan</p>
                    </div>
                    <a href="{{ route('pemilik-pengemudi.create', $kendaraan) }}"
                       class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-semibold transition flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Identitas
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($kendaraan->pemilikPengemudis as $pp)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">{{ $pp->nama }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">
                                        {{ $pp->peran_label }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 space-x-2">
                                    <span>No. SIM: <strong class="text-slate-700">{{ $pp->no_sim ?: '-' }}</strong></span>
                                    <span>•</span>
                                    <span>No. KTP: <strong class="text-slate-700">{{ $pp->no_ktp ?: '-' }}</strong></span>
                                    <span>•</span>
                                    <span>Telp: <strong class="text-slate-700">{{ $pp->no_telepon ?: '-' }}</strong></span>
                                </div>
                                <p class="text-[11px] text-slate-600">Alamat: {{ $pp->alamat ?: '-' }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('pemilik-pengemudi.edit', [$kendaraan, $pp]) }}"
                                   class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-[11px] font-medium transition">
                                    Edit
                                </a>
                                <form action="{{ route('pemilik-pengemudi.destroy', [$kendaraan, $pp]) }}" method="POST"
                                      onsubmit="return confirm('Hapus data identitas {{ $pp->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-[11px] font-medium transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs">
                            Belum ada data pemilik atau pengemudi yang dihubungkan ke kendaraan ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 1 Kolom Samping: Status Penertiban & Administrasi Berkas -->
        <div class="space-y-6">
            <!-- Box Penertiban Terkait -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100">
                    Surat Pernyataan / Penyitaan
                </h3>

                @if($kendaraan->penertibs->count() > 0)
                    <div class="space-y-3">
                        @foreach($kendaraan->penertibs as $p)
                            <div class="p-3 bg-blue-50/50 border border-blue-100 rounded-xl text-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 font-mono">{{ $p->nomor_surat ?: 'Tanpa No. Surat' }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $p->status === 'aktif' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $p->status_label }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500">Tgl Penertiban: {{ $p->tanggal_penertiban->format('d/m/Y') }}</p>
                                <p class="text-[11px] text-slate-600">Barang Bukti: {{ $p->barangBuktis->count() }} item disita</p>
                                <div class="pt-2 border-t border-blue-100/60 flex items-center justify-end">
                                    <a href="{{ route('penertiban.show', $p) }}" class="text-xs font-bold text-blue-700 hover:text-blue-900">
                                        Buka Dokumen Penyitaan &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-400">
                        <p>Belum ada tindakan surat penyitaan untuk kendaraan ini.</p>
                        <a href="{{ route('penertiban.create', ['kendaraan_id' => $kendaraan->id]) }}"
                           class="inline-block mt-3 px-3 py-1.5 bg-blue-700 text-white rounded-xl text-xs font-semibold hover:bg-blue-800 transition">
                            Buat Surat Penyitaan Sekarang
                        </a>
                    </div>
                @endif
            </div>

            <!-- Box Status Validasi & Verifikasi -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-xs space-y-3">
                <h3 class="font-bold text-blue-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                    Informasi Verifikasi
                </h3>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status Verifikasi:</span>
                    <span class="font-bold text-slate-800 capitalize">{{ $kendaraan->status_verifikasi }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Diverifikasi Oleh:</span>
                    <span class="text-slate-800">{{ $kendaraan->verifiedBy?->name ?? 'Belum diverifikasi' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Waktu Verifikasi:</span>
                    <span class="text-slate-800">{{ $kendaraan->verified_at ? $kendaraan->verified_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Status Penanganan Operasional:</span>
                    <span class="font-bold text-indigo-700 capitalize">{{ str_replace('_', ' ', $kendaraan->status_penanganan) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Data -->
    <div x-show="deleteModal" style="display:none;"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.away="deleteModal = false" class="bg-white rounded-2xl max-w-sm w-full p-6 text-center shadow-xl border border-slate-200">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Hapus Kendaraan</h3>
            <p class="text-xs text-slate-500 mt-2">
                Hapus data kendaraan <strong class="text-slate-800" x-text="deleteItemName"></strong> beserta berkas terkait?
            </p>
            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="deleteModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
