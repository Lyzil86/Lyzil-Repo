@extends('layouts.admin')

@section('title', 'Dashboard Ringkasan')
@section('page-title', 'Dashboard Administrasi')
@section('breadcrumb')
    <span>Beranda</span> / <span class="text-slate-600 font-medium">Statistik & Ringkasan Data</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#1a2744] to-blue-900 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="inline-block px-2.5 py-1 bg-blue-500/30 text-blue-200 text-xs font-semibold rounded-md mb-2">
                Dinas Perhubungan Kota Bogor • Bidang Angkutan
            </span>
            <h2 class="text-xl md:text-2xl font-bold">Basis Data Penertiban Angkutan Umum Usia > 20 Tahun</h2>
            <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                Berdasarkan UU No. 22/2009, Perda Kota Bogor No. 8/2023, dan Perwali No. 11/2026 tentang Rasionalisasi, Peremajaan, dan Penghapusan Kendaraan Bermotor Umum dalam Trayek.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('kendaraan.create') }}" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kendaraan
            </a>
            <a href="{{ route('penertiban.create') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Buat Surat Penyitaan
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Kendaraan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kendaraan Terdata</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_kendaraan']) }}</h3>
                <p class="text-xs text-blue-600 font-medium mt-1">Armada terdata di sistem</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 6h4l3 5v4h-2m-5-9H5l-2 5v4h2m0 0h8"/></svg>
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Verifikasi</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['menunggu_verifikasi']) }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Perlu pemeriksaan berkas</p>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card 3: Terverifikasi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kendaraan Terverifikasi</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['terverifikasi']) }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Divalidasi oleh verifikator</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Card 4: Selesai Ditertibkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Ditertibkan</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stats['selesai_ditertibkan']) }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Penanganan tuntas</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1: Berdasarkan Jenis Pelanggaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Distribusi Pelanggaran Berdasarkan Jenis Pelanggaran</h3>
                    <p class="text-xs text-slate-500">Jumlah armada & penyitaan berdasarkan batas usia (≥ 20 tahun), kelaikan teknis, dan administrasi</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="chartJenisPelanggaran"></canvas>
            </div>
        </div>

        <!-- Chart 2: Berdasarkan Tahun Kendaraan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Distribusi Tahun Pembuatan Armada</h3>
                    <p class="text-xs text-slate-500">Sebaran usia kendaraan angkutan umum yang ditertibkan</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="chartTahun"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row: Kendaraan Terbaru & Log Aktivitas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 2 Kolom: Data Kendaraan Terdata Terbaru -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Pendataan Kendaraan Terkini</h3>
                    <p class="text-xs text-slate-500">5 kendaraan terakhir yang diregistrasi ke sistem</p>
                </div>
                <a href="{{ route('kendaraan.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">No. Kendaraan</th>
                            <th class="px-4 py-3">Trayek</th>
                            <th class="px-4 py-3">Tahun / Usia</th>
                            <th class="px-4 py-3">Status Verifikasi</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($kendaraanTerbaru as $k)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-medium">
                                    <span class="font-bold text-slate-900 block">{{ $k->nomor_kendaraan }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $k->kode_data }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $k->trayek?->kode_trayek ? 'Trayek ' . $k->trayek->kode_trayek : '-' }}
                                    <span class="block text-[11px] text-slate-400">{{ $k->trayek?->nama_trayek }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $k->tahun_kendaraan }}
                                    <span class="block text-[11px] font-semibold text-rose-600">
                                        {{ $k->usia_operasional }} Tahun
                                    </span>
                                </td>
                                <td class="px-4 py-3">
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
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('kendaraan.show', $k) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-medium transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada data kendaraan terdata.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 1 Kolom: Aktivitas Administrasi Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Audit Aktivitas</h3>
                    <p class="text-xs text-slate-500">Riwayat aksi admin terbaru</p>
                </div>
                <a href="{{ route('audit-log.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                    Semua &rarr;
                </a>
            </div>
            <div class="p-4 flex-1 overflow-y-auto max-h-[380px] space-y-3">
                @forelse($aktivitasTerbaru as $log)
                    <div class="flex items-start gap-3 text-xs border-b border-slate-50 pb-2.5 last:border-0 last:pb-0">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold flex-shrink-0 text-[10px]">
                            {{ strtoupper(substr($log->aksi, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 font-medium truncate">{{ $log->deskripsi }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $log->user_name ?? 'Sistem' }} • <span class="font-mono text-[10px] text-slate-500 font-medium" title="{{ $log->created_at?->diffForHumans() }}">{{ $log->formatted_created_at }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-slate-400 py-6 text-xs">Belum ada riwayat aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart Jenis Pelanggaran
        const ctxJenis = document.getElementById('chartJenisPelanggaran');
        if (ctxJenis) {
            const dataJenis = @json($statsByJenisPelanggaran);
            const labels = dataJenis.map(item => item.kategori);
            const values = dataJenis.map(item => item.total);
            const colors = dataJenis.map(item => item.color);

            new Chart(ctxJenis, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Pelanggaran / Bukti',
                        data: values,
                        backgroundColor: colors,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.parsed.y + ' Kasus / Dokumen';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: {
                            ticks: {
                                maxRotation: 25,
                                minRotation: 0,
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }

        // Chart Tahun Kendaraan
        const ctxTahun = document.getElementById('chartTahun');
        if (ctxTahun) {
            const dataTahun = @json($statsByTahun);
            const labelsTahun = dataTahun.map(item => item.tahun_kendaraan);
            const valuesTahun = dataTahun.map(item => item.total);

            new Chart(ctxTahun, {
                type: 'line',
                data: {
                    labels: labelsTahun.length ? labelsTahun : ['Belum Ada Data'],
                    datasets: [{
                        label: 'Jumlah Armada',
                        data: valuesTahun.length ? valuesTahun : [0],
                        borderColor: '#0284c7',
                        backgroundColor: 'rgba(2, 132, 199, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#0284c7',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
