@extends('layouts.admin')

@section('title', 'Database Kendaraan')
@section('page-title', 'Database Kendaraan Angkutan')
@section('breadcrumb')
    <span>Database</span> / <span class="text-slate-600 font-medium">Daftar Kendaraan Terdata</span>
@endsection

@section('content')
<div class="space-y-5" x-data="{ deleteModal: false, deleteUrl: '', deleteItemName: '' }">
    <!-- Header Action & Quick Stat -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Data Armada Angkutan Terdata</h2>
            <p class="text-xs text-slate-500">Pendataan armada angkutan umum yang beroperasi di wilayah Kota Bogor.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kendaraan.create') }}"
               class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Registrasi Kendaraan Baru
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('kendaraan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Cari Teks -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Cari Data</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No. Kendaraan, Kode Data, Lokasi..."
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                </div>
            </div>

            <!-- Filter Trayek -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Trayek</label>
                <select name="trayek_id" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                    <option value="">Semua Trayek</option>
                    @foreach($trayeks as $trayek)
                        <option value="{{ $trayek->id }}" {{ request('trayek_id') == $trayek->id ? 'selected' : '' }}>
                            Trayek {{ $trayek->kode_trayek }} - {{ $trayek->nama_trayek }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Verifikasi -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Status Verifikasi</label>
                <select name="status_verifikasi" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status_verifikasi') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diverifikasi" {{ request('status_verifikasi') === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="ditolak" {{ request('status_verifikasi') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                    Filter Data
                </button>
                @if(request()->hasAny(['search', 'trayek_id', 'status_verifikasi', 'status_penanganan']))
                    <a href="{{ route('kendaraan.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition" title="Reset Filter">
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
                        <th class="px-4 py-3.5">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_data', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-900">
                                <span>Kode Data</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="px-4 py-3.5">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor_kendaraan', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-900">
                                <span>No. Polisi</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="px-4 py-3.5">Trayek</th>
                        <th class="px-4 py-3.5">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'tahun_kendaraan', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-900">
                                <span>Tahun / Usia</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="px-4 py-3.5">Lokasi Pelanggaran</th>
                        <th class="px-4 py-3.5">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal_pendataan', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-900">
                                <span>Tgl. Data</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            </a>
                        </th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($kendaraans as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-medium text-slate-600">
                                {{ $k->kode_data }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <span class="font-bold text-slate-900 block">{{ $k->nomor_kendaraan }}</span>
                                <span class="text-[11px] text-slate-400">{{ $k->jenis_kendaraan }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($k->nama_trayek)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold text-[11px]">
                                        {{ $k->nama_trayek }}
                                    </span>
                                @elseif($k->trayek)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold text-[11px]">
                                        Trayek {{ $k->trayek->kode_trayek }}
                                    </span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5 truncate max-w-[140px]">{{ $k->trayek->nama_trayek }}</span>
                                @else
                                    <span class="text-slate-400 italic">Tanpa Trayek</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-slate-800">{{ $k->tahun_kendaraan }}</span>
                                    @php $usia = $k->usia_operasional; @endphp
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $usia >= 20 ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $usia }} th
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 truncate max-w-[160px]" title="{{ $k->lokasi_pelanggaran }}">
                                {{ $k->lokasi_pelanggaran }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                                {{ $k->tanggal_pendataan->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($k->status_verifikasi === 'diverifikasi')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Diverifikasi
                                    </span>
                                @elseif($k->status_verifikasi === 'menunggu')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('kendaraan.show', $k) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('kendaraan.edit', $k) }}" class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button"
                                            @click="deleteModal = true; deleteUrl = '{{ route('kendaraan.destroy', $k) }}'; deleteItemName = '{{ $k->nomor_kendaraan }} ({{ $k->kode_data }})'"
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Kendaraan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 6h4l3 5v4h-2m-5-9H5l-2 5v4h2m0 0h8"/></svg>
                                    <p class="text-sm font-medium text-slate-600">Tidak ada data kendaraan ditemukan</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol registrasi untuk menambahkan kendaraan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kendaraans->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $kendaraans->links() }}
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
            <h3 class="text-base font-bold text-slate-800">Konfirmasi Penghapusan</h3>
            <p class="text-xs text-slate-500 mt-2">
                Apakah Anda yakin ingin menghapus data kendaraan <strong class="text-slate-800" x-text="deleteItemName"></strong>? Data ini akan dipindahkan ke arsip sampah.
            </p>
            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="deleteModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
