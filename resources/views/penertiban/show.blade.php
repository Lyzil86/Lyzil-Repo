@extends('layouts.admin')

@section('title', 'Berkas Penertiban ' . $penertib->nomor_surat)
@section('page-title', 'Detail Berkas Penertiban')
@section('breadcrumb')
    <span>Operasional</span> / <a href="{{ route('penertiban.index') }}" class="hover:underline">Penertiban</a> / <span class="text-slate-600 font-medium">{{ $penertib->nomor_surat ?: 'Berkas' }}</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ deleteModal: false, deleteUrl: '', deleteItemName: '' }">
    <!-- Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('penertiban.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-slate-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-800 font-mono">{{ $penertib->nomor_surat ?: 'SURAT PENYITAAN' }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $penertib->status === 'aktif' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $penertib->status_label }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Penertiban pada {{ $penertib->tanggal_penertiban->isoFormat('D MMMM Y') }} • Dibuat oleh {{ $penertib->createdBy?->name ?? 'Petugas' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('penertiban.cetak', $penertib) }}" target="_blank"
               class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Surat Penyitaan Resmi
            </a>
            <a href="{{ route('penertiban.edit', $penertib) }}"
               class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 2 Kolom Kiri: Rincian Surat & Barang Bukti -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Box Surat Penertiban Data -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    Keterangan Penindakan Sesuai Format Surat Pernyataan
                </h3>

                @php
                    $k = $penertib->kendaraan;
                    $pp = $k->pemilikPengemudis->first();
                @endphp

                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">a. Nama Pemilik / Pengemudi</span>
                        <span class="sm:col-span-2 text-slate-800 font-bold">
                            {{ $pp?->nama ?? 'Belum ada data pengemudi' }}
                            @if($pp)
                                <span class="ml-2 font-normal text-slate-500">({{ $pp->peran_label }})</span>
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">b. Nomor Uji Kendaraan</span>
                        <span class="sm:col-span-2 text-slate-800 font-mono font-semibold">{{ $pp?->no_sim ?: '-' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">c. Alamat</span>
                        <span class="sm:col-span-2 text-slate-800">{{ $pp?->alamat ?: '-' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">d. Nomor Kendaraan</span>
                        <span class="sm:col-span-2 text-slate-900 font-bold text-sm">
                            @if($k)
                                <a href="{{ route('kendaraan.show', $k) }}" class="text-blue-700 hover:underline">
                                    {{ $k->nomor_kendaraan }}
                                    @if($k->trashed())
                                        <span class="text-xs text-rose-500 font-normal">(Dihapus)</span>
                                    @endif
                                </a>
                                <span class="ml-2 text-xs font-normal text-slate-400 font-mono">({{ $k->kode_data }})</span>
                            @else
                                <span class="text-slate-400 italic font-normal">- (Data kendaraan telah dihapus)</span>
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">e. Jenis / Trayek Angkutan</span>
                        <span class="sm:col-span-2 text-slate-800 font-semibold">
                            @if($k)
                                {{ $k->jenis_kendaraan }}
                                @if($k->trayek)
                                    / Trayek {{ $k->trayek->kode_trayek }} ({{ $k->trayek->nama_trayek }})
                                @endif
                            @else
                                <span class="text-slate-400 italic font-normal">-</span>
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">f. Usia Teknis Operasional</span>
                        <span class="sm:col-span-2 text-rose-700 font-bold">
                            {{ $penertib->usia_teknis_operasional ?? $k->usia_operasional }} Tahun
                            <span class="text-slate-500 font-normal">(Tahun Kendaraan: {{ $k->tahun_kendaraan }})</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">g. Lokasi Pelanggaran</span>
                        <span class="sm:col-span-2 text-slate-800 font-semibold">{{ $penertib->lokasi_penertiban }}</span>
                    </div>
                </div>
            </div>

            <!-- Box Barang Bukti yang Disita -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider">
                            Barang Bukti yang Disita
                        </h3>
                        <p class="text-[11px] text-slate-400">Daftar dokumen dan unit yang disita oleh petugas PPNS</p>
                    </div>
                    <a href="{{ route('barang-bukti.create', ['penertib_id' => $penertib->id]) }}"
                       class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-semibold transition flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Barang Bukti
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2 w-12 text-center">No</th>
                                <th class="px-3 py-2">Jenis Barang Bukti</th>
                                <th class="px-3 py-2">Nomor Dokumen</th>
                                <th class="px-3 py-2">Status / Kondisi</th>
                                <th class="px-3 py-2">Keterangan</th>
                                <th class="px-3 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($penertib->barangBuktis as $index => $bb)
                                <tr>
                                    <td class="px-3 py-2.5 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2.5 font-bold text-slate-800">
                                        <div class="flex items-center gap-2">
                                            @if($bb->foto)
                                                <a href="{{ route('barang-bukti.show', $bb) }}" title="Lihat Foto Bukti">
                                                    <img src="{{ $bb->foto_url }}" alt="Foto" class="w-7 h-7 object-cover rounded border border-slate-200 shadow-xs">
                                                </a>
                                            @endif
                                            <a href="{{ route('barang-bukti.show', $bb) }}" class="hover:text-blue-700">
                                                {{ $bb->jenis_label }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 font-mono text-slate-700">
                                        {{ $bb->nomor_dokumen ?: '-' }}
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $bb->status === 'disita' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ ucfirst($bb->status) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">Kondisi: {{ ucfirst($bb->kondisi) }}</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-500">
                                        {{ $bb->keterangan ?: '-' }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('barang-bukti.show', $bb) }}" class="p-1 text-slate-500 hover:text-blue-600 rounded" title="Lihat Detail & Foto">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('barang-bukti.edit', $bb) }}" class="p-1 text-slate-500 hover:text-amber-600 rounded" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('barang-bukti.destroy', $bb) }}" method="POST"
                                                  onsubmit="return confirm('Hapus barang bukti ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-500 hover:text-rose-600 rounded cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-slate-400">
                                        Belum ada barang bukti disita yang dicatatkan pada berkas ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 1 Kolom Kanan: Dasar Hukum, TTD & Catatan Internal -->
        <div class="space-y-6">
            <!-- Box Konsiderans Hukum -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-xs space-y-3">
                <h3 class="font-bold text-blue-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                    Dasar Hukum Penindakan
                </h3>
                <p class="text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100 text-[11px]">
                    {{ $penertib->dasar_hukum }}
                </p>
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-[11px] font-semibold leading-relaxed">
                    "Kendaraan yang melebihi usia teknis TIDAK AKAN BEROPERASI KEMBALI sebagai angkutan umum/penumpang di wilayah Kota Bogor dan seluruh jalur/trayek yang terkait."
                </div>
            </div>

            <!-- Box Pejabat PPNS -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-xs space-y-3">
                <h3 class="font-bold text-blue-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                    Pejabat Penanda Tangan
                </h3>
                <div>
                    <span class="text-slate-400 block text-[11px]">Pejabat PPNS:</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $penertib->petugas_penertiban ?: 'FAZAL RACHMAN SE' }}</span>
                    <span class="text-[11px] text-slate-500 block">Dinas Perhubungan Kota Bogor</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Tanggal Penetapan Surat:</span>
                    <span class="text-slate-800 font-semibold">{{ $penertib->tanggal_surat ? $penertib->tanggal_surat->isoFormat('D MMMM Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Catatan Internal:</span>
                    <p class="text-slate-600 text-[11px] mt-1">{{ $penertib->catatan_internal ?: 'Tidak ada catatan internal.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
