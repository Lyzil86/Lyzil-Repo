@extends('layouts.admin')

@section('title', 'Data Penertiban & Penyitaan')
@section('page-title', 'Data Penertiban & Penyitaan')
@section('breadcrumb')
    <span>Operasional</span> / <span class="text-slate-600 font-medium">Data Penertiban</span>
@endsection

@section('content')
<div class="space-y-5" x-data="{ deleteModal: false, deleteUrl: '', deleteItemName: '' }">
    <!-- Header Action & Quick Stat -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Tindakan Penertiban Angkutan</h2>
            <p class="text-xs text-slate-500">Rekapitulasi berkas penindakan dan penyitaan armada angkutan melebihi 20 tahun.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penertiban.create') }}"
               class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Surat Penyitaan Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('penertiban.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Cari Data</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No. Surat, No. Kendaraan, Lokasi..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Status Penindakan</label>
                <select name="status" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Tanggal Penindakan</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter Data
                </button>
                @if(request()->hasAny(['search', 'status', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('penertiban.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Nomor Surat</th>
                        <th class="px-4 py-3.5">Kendaraan</th>
                        <th class="px-4 py-3.5">Trayek</th>
                        <th class="px-4 py-3.5">Lokasi & Tgl Penindakan</th>
                        <th class="px-4 py-3.5">Petugas PPNS</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($penertibs as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 font-mono block">
                                    {{ $p->nomor_surat ?: '500/ ... - Daltib' }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    Tgl Surat: {{ $p->tanggal_surat ? $p->tanggal_surat->format('d/m/Y') : '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($p->kendaraan)
                                    <a href="{{ route('kendaraan.show', $p->kendaraan) }}" class="font-bold text-blue-700 hover:underline block">
                                        {{ $p->kendaraan->nomor_kendaraan }}
                                        @if($p->kendaraan->trashed())
                                            <span class="text-[10px] text-rose-500 font-normal">(Dihapus)</span>
                                        @endif
                                    </a>
                                    <span class="text-[11px] text-slate-400">
                                        Tahun {{ $p->kendaraan->tahun_kendaraan }} ({{ $p->usia_teknis_operasional ?? $p->kendaraan->usia_operasional }} th)
                                    </span>
                                @else
                                    <span class="font-bold text-slate-400 italic block">- (Kendaraan dihapus)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($p->kendaraan?->trayek)
                                    <span class="font-semibold text-slate-800">Trayek {{ $p->kendaraan->trayek->kode_trayek }}</span>
                                    <span class="block text-[11px] text-slate-400 truncate max-w-[120px]">{{ $p->kendaraan->trayek->nama_trayek }}</span>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800 block truncate max-w-[150px]">{{ $p->lokasi_penertiban }}</span>
                                <span class="text-[11px] text-slate-500">{{ $p->tanggal_penertiban->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $p->petugas_penertiban ?: 'PPNS Dishub' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($p->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Aktif (Disita)
                                    </span>
                                @elseif($p->status === 'selesai')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Selesai
                                    </span>
                                @elseif($p->status === 'draft')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        Draft
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('penertiban.cetak', $p) }}" target="_blank"
                                       class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" title="Cetak Surat Penyitaan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <a href="{{ route('penertiban.show', $p) }}"
                                       class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Buka Berkas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('penertiban.edit', $p) }}"
                                       class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button"
                                            @click="deleteModal = true; deleteUrl = '{{ route('penertiban.destroy', $p) }}'; deleteItemName = '{{ $p->nomor_surat ?: 'Surat Penyitaan' }}'"
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Berkas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                Belum ada berkas penindakan atau surat penyitaan terdata.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penertibs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $penertibs->links() }}
            </div>
        @endif
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
            <h3 class="text-base font-bold text-slate-800">Hapus Berkas Penertiban</h3>
            <p class="text-xs text-slate-500 mt-2">
                Hapus berkas <strong class="text-slate-800" x-text="deleteItemName"></strong>? Seluruh data barang bukti yang terkait akan ikut terhapus.
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
