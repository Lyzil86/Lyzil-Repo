@extends('layouts.admin')

@section('title', 'Laporan & Rekapitulasi')
@section('page-title', 'Laporan & Rekapitulasi Data')
@section('breadcrumb')
    <span>Laporan</span> / <span class="text-slate-600 font-medium">Rekapitulasi Penertiban Angkutan</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header & Ekspor Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Laporan Penertiban Angkutan Usia > 20 Tahun</h2>
            <p class="text-xs text-slate-500">Rekapitulasi berkas operasional yang dapat difilter dan diekspor ke berbagai format dokumen resmi.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.csv', request()->query()) }}"
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor Excel / CSV
            </a>
            <a href="{{ route('laporan.pdf', request()->query()) }}" target="_blank"
               class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Unduh PDF
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Trayek</label>
                <select name="trayek_id" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <option value="">Semua Trayek</option>
                    @foreach($trayeks as $trayek)
                        <option value="{{ $trayek->id }}" {{ request('trayek_id') == $trayek->id ? 'selected' : '' }}>
                            Trayek {{ $trayek->kode_trayek }} - {{ $trayek->nama_trayek }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Status Verifikasi</label>
                <select name="status_verifikasi" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status_verifikasi') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diverifikasi" {{ request('status_verifikasi') === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="ditolak" {{ request('status_verifikasi') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Status Penanganan</label>
                <select name="status_penanganan" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <option value="">Semua Status</option>
                    <option value="belum_ditangani" {{ request('status_penanganan') === 'belum_ditangani' ? 'selected' : '' }}>Belum Ditangani</option>
                    <option value="sedang_diproses" {{ request('status_penanganan') === 'sedang_diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ request('status_penanganan') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibebaskan" {{ request('status_penanganan') === 'dibebaskan' ? 'selected' : '' }}>Dibebaskan</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Periode Dari</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Periode Sampai</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                           class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    <button type="submit" class="py-2 px-4 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ringkasan Hasil Filter -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <p class="text-[11px] font-semibold text-slate-500 uppercase">Armada Terpilih</p>
            <h4 class="text-xl font-bold text-slate-800 mt-1">{{ number_format($summary['total']) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <p class="text-[11px] font-semibold text-amber-600 uppercase">Menunggu Validasi</p>
            <h4 class="text-xl font-bold text-amber-600 mt-1">{{ number_format($summary['menunggu']) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <p class="text-[11px] font-semibold text-emerald-600 uppercase">Tervalidasi</p>
            <h4 class="text-xl font-bold text-emerald-600 mt-1">{{ number_format($summary['diverifikasi']) }}</h4>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200">
            <p class="text-[11px] font-semibold text-indigo-600 uppercase">Tuntas Ditertibkan</p>
            <h4 class="text-xl font-bold text-indigo-600 mt-1">{{ number_format($summary['selesai']) }}</h4>
        </div>
    </div>

    <!-- Hasil Tabel Laporan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Hasil Rekapitulasi Data</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $kendaraans->count() }} dari {{ $kendaraans->total() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Kode Data</th>
                        <th class="px-4 py-3">No. Kendaraan</th>
                        <th class="px-4 py-3">Trayek</th>
                        <th class="px-4 py-3">Tahun / Usia</th>
                        <th class="px-4 py-3">Lokasi Penindakan</th>
                        <th class="px-4 py-3">Tgl. Data</th>
                        <th class="px-4 py-3">Status Verifikasi</th>
                        <th class="px-4 py-3">Status Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($kendaraans as $k)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 font-mono font-medium text-slate-600">{{ $k->kode_data }}</td>
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $k->nomor_kendaraan }}</td>
                            <td class="px-4 py-3">
                                {{ $k->trayek?->kode_trayek ? 'Trayek ' . $k->trayek->kode_trayek : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $k->tahun_kendaraan }}
                                <span class="font-bold text-rose-700 ml-1">({{ $k->usia_operasional }} th)</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 max-w-xs truncate">{{ $k->lokasi_pelanggaran }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $k->tanggal_pendataan->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="capitalize font-semibold {{ $k->status_verifikasi === 'diverifikasi' ? 'text-emerald-700' : 'text-amber-700' }}">
                                    {{ $k->status_verifikasi }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="capitalize text-slate-700">{{ str_replace('_', ' ', $k->status_penanganan) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                Tidak ada data yang sesuai dengan filter yang dipilih.
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
</div>
@endsection
