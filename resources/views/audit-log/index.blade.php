@extends('layouts.admin')

@section('title', 'Audit Log Sistem')
@section('page-title', 'Jejak Audit Aktivitas (Audit Log)')
@section('breadcrumb')
    <span>Sistem Keamanan</span> / <span class="text-slate-600 font-medium">Log Aktivitas Petugas</span>
@endsection

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Riwayat & Log Keamanan Sistem</h2>
            <p class="text-xs text-slate-500">Mencatat seluruh aksi penambahan, perubahan, penghapusan, verifikasi, login, logout, dan ekspor data.</p>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('audit-log.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Cari Keterangan / Admin</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, nama admin, deskripsi..."
                       class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Jenis Aksi</label>
                <select name="aksi" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white uppercase">
                    <option value="">Semua Aksi</option>
                    @foreach($aksiList as $a)
                        <option value="{{ $a }}" {{ request('aksi') === $a ? 'selected' : '' }}>{{ strtoupper($a) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                       class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                    Filter Log
                </button>
                @if(request()->hasAny(['search', 'aksi', 'tanggal_dari', 'tanggal_sampai']))
                    <a href="{{ route('audit-log.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
         x-data="{ detailModal: false, selectedLog: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-48 whitespace-nowrap">Waktu Log (WIB)</th>
                        <th class="px-4 py-3.5 w-40">Petugas / Admin</th>
                        <th class="px-4 py-3.5 w-24">Aksi</th>
                        <th class="px-4 py-3.5">Deskripsi Tindakan</th>
                        <th class="px-4 py-3.5 w-28">IP Address</th>
                        <th class="px-4 py-3.5 w-16 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600 font-mono text-[11px] font-semibold">
                                {{ $log->formatted_created_at }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                {{ $log->user_name ?? ($log->user?->name ?? 'Sistem') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeColor = match($log->aksi) {
                                        'login' => 'bg-blue-100 text-blue-800',
                                        'logout' => 'bg-slate-100 text-slate-700',
                                        'create' => 'bg-emerald-100 text-emerald-800',
                                        'update' => 'bg-amber-100 text-amber-800',
                                        'delete' => 'bg-rose-100 text-rose-800',
                                        'export' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $badgeColor }}">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-800">
                                <span class="font-medium">{{ $log->deskripsi }}</span>
                                @if($log->model)
                                    <span class="text-[10px] text-slate-400 block font-mono">Model: {{ $log->model }} (ID: {{ $log->model_id ?? '-' }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button type="button"
                                        @click="selectedLog = {{ json_encode([
                                            'id' => $log->id,
                                            'waktu' => $log->formatted_created_at,
                                            'user_name' => $log->user_name ?? ($log->user?->name ?? 'Sistem'),
                                            'aksi' => $log->aksi,
                                            'deskripsi' => $log->deskripsi,
                                            'model' => $log->model,
                                            'model_id' => $log->model_id,
                                            'ip_address' => $log->ip_address ?: '127.0.0.1',
                                            'user_agent' => $log->user_agent,
                                            'data_lama' => $log->data_lama,
                                            'data_baru' => $log->data_baru,
                                        ]) }}; detailModal = true"
                                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition cursor-pointer"
                                        title="Lihat Detail Aktivitas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                Belum ada catatan riwayat audit log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif

        <!-- Modal Detail Aktivitas -->
        <div x-show="detailModal" style="display:none;"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="detailModal = false" class="bg-white rounded-2xl max-w-xl w-full p-6 text-left shadow-xl border border-slate-200" x-show="selectedLog">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800" x-text="selectedLog?.aksi"></span>
                        <h3 class="text-sm font-bold text-slate-800">Detail Aktivitas Petugas</h3>
                    </div>
                    <button type="button" @click="detailModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3 text-xs">
                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Waktu (WIB):</span>
                        <span class="col-span-2 font-mono font-bold text-slate-800" x-text="selectedLog?.waktu"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Petugas:</span>
                        <span class="col-span-2 font-semibold text-slate-900" x-text="selectedLog?.user_name"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Tindakan / Aksi:</span>
                        <span class="col-span-2 font-semibold uppercase text-slate-800" x-text="selectedLog?.aksi"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Deskripsi:</span>
                        <span class="col-span-2 text-slate-800" x-text="selectedLog?.deskripsi"></span>
                    </div>
                    <template x-if="selectedLog?.model">
                        <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">Model Terkait:</span>
                            <span class="col-span-2 font-mono text-slate-700" x-text="selectedLog?.model + ' (ID: ' + (selectedLog?.model_id ?? '-') + ')'"></span>
                        </div>
                    </template>
                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">IP Address:</span>
                        <span class="col-span-2 font-mono text-slate-700" x-text="selectedLog?.ip_address"></span>
                    </div>
                    <template x-if="selectedLog?.user_agent">
                        <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">User Agent:</span>
                            <span class="col-span-2 text-slate-600 break-all text-[11px]" x-text="selectedLog?.user_agent"></span>
                        </div>
                    </template>

                    <template x-if="selectedLog?.data_lama">
                        <div class="mt-3">
                            <p class="font-semibold text-slate-700 mb-1">Data Sebelum Perubahan:</p>
                            <pre class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-[11px] font-mono text-slate-700 overflow-x-auto max-h-32" x-text="JSON.stringify(selectedLog?.data_lama, null, 2)"></pre>
                        </div>
                    </template>

                    <template x-if="selectedLog?.data_baru">
                        <div class="mt-3">
                            <p class="font-semibold text-slate-700 mb-1">Data Sesudah Perubahan:</p>
                            <pre class="bg-slate-50 p-2.5 rounded-lg border border-slate-200 text-[11px] font-mono text-slate-700 overflow-x-auto max-h-32" x-text="JSON.stringify(selectedLog?.data_baru, null, 2)"></pre>
                        </div>
                    </template>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="button" @click="detailModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
