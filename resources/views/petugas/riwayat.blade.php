@extends('layouts.petugas')

@section('title', 'Riwayat Laporan Saya')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Riwayat Laporan Penertiban Saya</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh kendaraan angkutan yang Anda laporkan ke dalam sistem.</p>
        </div>
        <a href="{{ route('petugas.input') }}"
           class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Input Laporan Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Kode Data</th>
                        <th class="px-4 py-3.5">Armada / Jenis</th>
                        <th class="px-4 py-3.5">Trayek</th>
                        <th class="px-4 py-3.5">Tahun / Usia</th>
                        <th class="px-4 py-3.5">Jenis Pelanggaran</th>
                        <th class="px-4 py-3.5">Lokasi Pendataan</th>
                        <th class="px-4 py-3.5">Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($kendaraans as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono font-medium text-slate-600">
                                {{ $k->kode_data }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 block">{{ $k->nomor_kendaraan }}</span>
                                <span class="text-[11px] text-slate-400">{{ $k->jenis_kendaraan }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-medium text-[11px]">
                                    {{ $k->trayek_teks }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-800">{{ $k->tahun_kendaraan }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $k->is_melanggar_batas_usia ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                        {{ $k->usia_operasional }} Th
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-slate-700 block">
                                    {{ $k->jenis_pelanggaran_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 max-w-[200px] truncate" title="{{ $k->lokasi_pelanggaran }}">
                                {{ $k->lokasi_pelanggaran }}
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $k->tanggal_pendataan->isoFormat('D MMM Y') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($k->status_verifikasi === 'diverifikasi')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Terverifikasi
                                    </span>
                                @elseif($k->status_verifikasi === 'menunggu')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu Verifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                Belum ada laporan kendaraan yang Anda masukkan. Klik "Input Laporan Baru" untuk memulai pendataan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kendaraans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $kendaraans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
