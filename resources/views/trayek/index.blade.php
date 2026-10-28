@extends('layouts.admin')

@section('title', 'Data Trayek Angkutan')
@section('page-title', 'Manajemen Trayek Angkutan')
@section('breadcrumb')
    <span>Master Data</span> / <span class="text-slate-600 font-medium">Jalur & Trayek Kota Bogor</span>
@endsection

@section('content')
<div class="space-y-5" x-data="{ deleteModal: false, deleteUrl: '', deleteItemName: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Daftar Trayek Angkutan Umum Kota Bogor</h2>
            <p class="text-xs text-slate-500">Pengelolaan jalur izin operasional angkutan kota resmi.</p>
        </div>
        <a href="{{ route('trayek.create') }}"
           class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Trayek Baru
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('trayek.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari kode trayek, nama, asal, tujuan..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <select name="status" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="diarsipkan" {{ request('status') === 'diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
                <button type="submit" class="py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-24">Kode</th>
                        <th class="px-4 py-3.5">Nama Trayek</th>
                        <th class="px-4 py-3.5">Rute Asal &rarr; Tujuan</th>
                        <th class="px-4 py-3.5 text-center">Armada Terdata</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($trayeks as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-bold text-blue-700">
                                {{ $t->kode_trayek }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                {{ $t->nama_trayek }}
                                @if($t->deskripsi)
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5 truncate max-w-xs">{{ $t->deskripsi }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="font-medium text-slate-800">{{ $t->asal ?: '-' }}</span>
                                <span class="text-slate-400 mx-1">&rarr;</span>
                                <span class="font-medium text-slate-800">{{ $t->tujuan ?: '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $t->kendaraans_count }} unit
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($t->status === 'aktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Aktif
                                    </span>
                                @elseif($t->status === 'nonaktif')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Nonaktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        Diarsipkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('trayek.edit', $t) }}" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button"
                                            @click="deleteModal = true; deleteUrl = '{{ route('trayek.destroy', $t) }}'; deleteItemName = 'Trayek {{ $t->kode_trayek }}'"
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                Tidak ada data trayek ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trayeks->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $trayeks->links() }}
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
            <h3 class="text-base font-bold text-slate-800">Hapus Trayek</h3>
            <p class="text-xs text-slate-500 mt-2">
                Hapus master data <strong class="text-slate-800" x-text="deleteItemName"></strong>? Trayek yang memiliki relasi kendaraan tidak dapat dihapus.
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
