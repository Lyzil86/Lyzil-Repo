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
                        <th class="px-4 py-3.5">Lokasi & Bukti</th>
                        <th class="px-4 py-3.5">Status Verifikasi</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
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
                            <td class="px-4 py-3 text-slate-600 max-w-[220px]">
                                <div class="truncate font-medium text-slate-800" title="{{ $k->lokasi_pelanggaran }}">
                                    {{ $k->lokasi_pelanggaran }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                    @if($k->has_foto_bukti)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] bg-blue-50 text-blue-700 border border-blue-200">
                                            📷 {{ count($k->foto_bukti) }} Foto
                                        </span>
                                    @endif
                                    @if($k->has_koordinat)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                                            📍 GPS
                                        </span>
                                    @endif
                                    <span class="text-[10px] text-slate-400">{{ $k->tanggal_pendataan->isoFormat('D MMM Y') }}</span>
                                </div>
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
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('kendaraan.show', $k) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail & Foto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('kendaraan.edit', $k) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition" title="Edit Laporan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
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
