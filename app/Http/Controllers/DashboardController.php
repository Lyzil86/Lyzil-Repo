<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BarangBukti;
use App\Models\Kendaraan;
use App\Models\Penertib;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_kendaraan' => Kendaraan::count(),
            'menunggu_verifikasi' => Kendaraan::where('status_verifikasi', 'menunggu')->count(),
            'terverifikasi' => Kendaraan::where('status_verifikasi', 'diverifikasi')->count(),
            'selesai_ditertibkan' => Kendaraan::where('status_penanganan', 'selesai')->count(),
            'total_penertiban' => Penertib::count(),
            'penertiban_aktif' => Penertib::where('status', 'aktif')->count(),
        ];

        // Statistik Distribusi Pelanggaran Berdasarkan Jenis Pelanggaran (Dinamis dari Database)
        $currentYear = (int) date('Y');
        $batasUsiaCount = Kendaraan::where('tahun_kendaraan', '<=', $currentYear - 20)->count();
        $teknisFisikCount = Kendaraan::where('jenis_pelanggaran', 'pelanggaran_teknis')->count();
        $adminKirCount = BarangBukti::where('jenis', 'kir')->count();
        $adminKpsCount = BarangBukti::where('jenis', 'kps')->count();
        $adminStnkCount = BarangBukti::where('jenis', 'stnk')->count();
        $adminKendaraanCount = Kendaraan::where('jenis_pelanggaran', 'pelanggaran_administrasi')->count();
        $adminLainnyaCount = BarangBukti::whereNotIn('jenis', ['kir', 'kps', 'stnk'])->count();

        $statsByJenisPelanggaran = [
            [
                'kategori' => 'Batas Usia Teknis (≥ 20 Thn)',
                'total' => $batasUsiaCount,
                'color' => '#dc2626',
            ],
            [
                'kategori' => 'Pelanggaran Teknis Kendaraan',
                'total' => $teknisFisikCount,
                'color' => '#ea580c',
            ],
            [
                'kategori' => 'Administrasi Buku Uji (KIR)',
                'total' => $adminKirCount,
                'color' => '#2563eb',
            ],
            [
                'kategori' => 'Administrasi Izin Trayek (KPS)',
                'total' => $adminKpsCount,
                'color' => '#0284c7',
            ],
            [
                'kategori' => 'Administrasi Dokumen STNK',
                'total' => $adminStnkCount,
                'color' => '#4f46e5',
            ],
            [
                'kategori' => 'Administrasi Dokumen Lainnya',
                'total' => $adminKendaraanCount + $adminLainnyaCount,
                'color' => '#0d9488',
            ],
        ];

        // Tetap sediakan $statsByTrayek untuk kompatibilitas data
        $statsByTrayek = Kendaraan::select('trayek_id', DB::raw('count(*) as total'))
            ->with('trayek')
            ->groupBy('trayek_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Statistik berdasarkan tahun kendaraan
        $statsByTahun = Kendaraan::select('tahun_kendaraan', DB::raw('count(*) as total'))
            ->groupBy('tahun_kendaraan')
            ->orderBy('tahun_kendaraan')
            ->get();

        // Statistik pendataan per bulan (tahun ini)
        $statsByBulan = Kendaraan::select(
            DB::raw("strftime('%m', tanggal_pendataan) as bulan"),
            DB::raw('count(*) as total')
        )
            ->whereYear('tanggal_pendataan', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Aktivitas terbaru
        $aktivitasTerbaru = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Kendaraan terbaru
        $kendaraanTerbaru = Kendaraan::with('trayek')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'statsByJenisPelanggaran', 'statsByTrayek', 'statsByTahun', 'statsByBulan',
            'aktivitasTerbaru', 'kendaraanTerbaru'
        ));
    }
}
