@extends('layouts.admin')

@section('title', 'Database Barang Bukti')
@section('page-title', 'Manajemen Barang Bukti')
@section('breadcrumb')
    <span>Operasional</span> / <span class="text-slate-600 font-medium">Barang Bukti Disita</span>
@endsection

@section('content')
<div class="space-y-5" x-data="{ deleteModal: false, deleteUrl: '', deleteItemName: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Inventaris Barang Bukti Penertiban</h2>
            <p class="text-xs text-slate-500">Pencatatan KIR, KPS, STNK, dan fisik armada yang disita dari razia penertiban usia operasional.</p>
        </div>
        <a href="{{ route('barang-bukti.create') }}"
           class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Barang Bukti Baru
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('barang-bukti.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Cari Dokumen</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No. Dokumen, No. Kendaraan, No. Surat..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Jenis Bukti</label>
                <select name="jenis" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <option value="">Semua Jenis</option>
                    <option value="kir" {{ request('jenis') === 'kir' ? 'selected' : '' }}>Buku Uji Berkala (KIR)</option>
                    <option value="kps" {{ request('jenis') === 'kps' ? 'selected' : '' }}>Kartu Pengawasan (KPS)</option>
                    <option value="stnk" {{ request('jenis') === 'stnk' ? 'selected' : '' }}>STNK</option>
                    <option value="kendaraan" {{ request('jenis') === 'kendaraan' ? 'selected' : '' }}>Kendaraan Fisik</option>
                    <option value="lainnya" {{ request('jenis') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Status Penguasaan</label>
                <select name="status" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <option value="">Semua Status</option>
                    <option value="disita" {{ request('status') === 'disita' ? 'selected' : '' }}>Disita</option>
                    <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="dimusnahkan" {{ request('status') === 'dimusnahkan' ? 'selected' : '' }}>Dimusnahkan</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'jenis', 'status']))
                    <a href="{{ route('barang-bukti.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
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
                        <th class="px-4 py-3.5 w-16 text-center">Foto</th>
                        <th class="px-4 py-3.5">Jenis Barang Bukti</th>
                        <th class="px-4 py-3.5">Nomor Dokumen</th>
                        <th class="px-4 py-3.5">No. Surat Penertiban</th>
                        <th class="px-4 py-3.5">Kendaraan Objek</th>
                        <th class="px-4 py-3.5">Status & Kondisi</th>
                        <th class="px-4 py-3.5">Keterangan</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($barangBuktis as $bb)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-center">
                                @if($bb->foto)
                                    <a href="{{ route('barang-bukti.show', $bb) }}" class="inline-block relative group" title="Lihat Foto Bukti">
                                        <img src="{{ $bb->foto_url }}" alt="Foto" class="w-9 h-9 object-cover rounded-lg border border-slate-200 shadow-sm mx-auto group-hover:ring-2 group-hover:ring-blue-500 transition">
                                    </a>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-400" title="Tidak ada foto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                <a href="{{ route('barang-bukti.show', $bb) }}" class="hover:text-blue-700">
                                    {{ $bb->jenis_label }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-mono font-medium text-slate-700">
                                {{ $bb->nomor_dokumen ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($bb->penertib)
                                    <a href="{{ route('penertiban.show', $bb->penertib) }}" class="font-bold text-blue-700 hover:underline">
                                        {{ $bb->penertib->nomor_surat ?: 'Surat #' . $bb->penertib->id }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($bb->penertib?->kendaraan)
                                    <a href="{{ route('kendaraan.show', $bb->penertib->kendaraan) }}" class="font-bold text-slate-800 hover:underline">
                                        {{ $bb->penertib->kendaraan->nomor_kendaraan }}
                                        @if($bb->penertib->kendaraan->trashed())
                                            <span class="text-[10px] text-rose-500 font-normal">(Dihapus)</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $bb->status === 'disita' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ ucfirst($bb->status) }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Kondisi: {{ ucfirst($bb->kondisi) }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 max-w-[180px] truncate" title="{{ $bb->keterangan }}">
                                {{ $bb->keterangan ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('barang-bukti.show', $bb) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail & Foto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('barang-bukti.edit', $bb) }}" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button"
                                            @click="deleteModal = true; deleteUrl = '{{ route('barang-bukti.destroy', $bb) }}'; deleteItemName = '{{ $bb->jenis_label }}'"
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                Tidak ada data barang bukti ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangBuktis->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $barangBuktis->links() }}
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
            <h3 class="text-base font-bold text-slate-800">Hapus Barang Bukti</h3>
            <p class="text-xs text-slate-500 mt-2">
                Hapus pencatatan barang bukti <strong class="text-slate-800" x-text="deleteItemName"></strong>?
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
