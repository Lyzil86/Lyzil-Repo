<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kendaraan;
use App\Models\Trayek;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with(['trayek', 'penertibs'])
            ->when($request->trayek_id, fn ($q) => $q->where('trayek_id', $request->trayek_id))
            ->when($request->status_verifikasi, fn ($q) => $q->where('status_verifikasi', $request->status_verifikasi))
            ->when($request->status_penanganan, fn ($q) => $q->where('status_penanganan', $request->status_penanganan))
            ->when($request->tahun_kendaraan, fn ($q) => $q->where('tahun_kendaraan', $request->tahun_kendaraan))
            ->when($request->tanggal_dari, fn ($q) => $q->where('tanggal_pendataan', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->where('tanggal_pendataan', '<=', $request->tanggal_sampai));

        $kendaraans = $query->orderBy('tanggal_pendataan', 'desc')->paginate(20)->withQueryString();
        $trayeks = Trayek::where('status', 'aktif')->orderBy('kode_trayek')->get();

        $summary = [
            'total' => $query->count(),
            'menunggu' => (clone $query)->where('status_verifikasi', 'menunggu')->count(),
            'diverifikasi' => (clone $query)->where('status_verifikasi', 'diverifikasi')->count(),
            'selesai' => (clone $query)->where('status_penanganan', 'selesai')->count(),
        ];

        return view('laporan.index', compact('kendaraans', 'trayeks', 'summary'));
    }

    public function exportCsv(Request $request)
    {
        AuditLog::catat('export', 'Ekspor laporan CSV kendaraan');

        $query = Kendaraan::with(['trayek'])
            ->when($request->trayek_id, fn ($q) => $q->where('trayek_id', $request->trayek_id))
            ->when($request->status_verifikasi, fn ($q) => $q->where('status_verifikasi', $request->status_verifikasi))
            ->when($request->status_penanganan, fn ($q) => $q->where('status_penanganan', $request->status_penanganan))
            ->when($request->tahun_kendaraan, fn ($q) => $q->where('tahun_kendaraan', $request->tahun_kendaraan))
            ->when($request->tanggal_dari, fn ($q) => $q->where('tanggal_pendataan', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->where('tanggal_pendataan', '<=', $request->tanggal_sampai))
            ->orderBy('tanggal_pendataan', 'desc')
            ->get();

        $filename = 'laporan-kendaraan-'.date('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            // BOM untuk UTF-8 Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Kode Data', 'Nomor Kendaraan', 'Jenis Kendaraan', 'Trayek',
                'Tahun Kendaraan', 'Usia (Tahun)', 'Lokasi Pelanggaran',
                'Tanggal Pendataan', 'Status Verifikasi', 'Status Penanganan', 'Catatan',
            ]);
            foreach ($query as $k) {
                fputcsv($handle, [
                    $k->kode_data,
                    $k->nomor_kendaraan,
                    $k->jenis_kendaraan,
                    $k->trayek?->nama_trayek ?? '-',
                    $k->tahun_kendaraan,
                    date('Y') - $k->tahun_kendaraan,
                    $k->lokasi_pelanggaran,
                    $k->tanggal_pendataan->format('d/m/Y'),
                    $k->status_verifikasi,
                    $k->status_penanganan,
                    $k->catatan ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        AuditLog::catat('export', 'Ekspor laporan PDF kendaraan');

        $kendaraans = Kendaraan::with(['trayek'])
            ->when($request->trayek_id, fn ($q) => $q->where('trayek_id', $request->trayek_id))
            ->when($request->status_verifikasi, fn ($q) => $q->where('status_verifikasi', $request->status_verifikasi))
            ->when($request->status_penanganan, fn ($q) => $q->where('status_penanganan', $request->status_penanganan))
            ->when($request->tahun_kendaraan, fn ($q) => $q->where('tahun_kendaraan', $request->tahun_kendaraan))
            ->when($request->tanggal_dari, fn ($q) => $q->where('tanggal_pendataan', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->where('tanggal_pendataan', '<=', $request->tanggal_sampai))
            ->orderBy('tanggal_pendataan', 'desc')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('kendaraans'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kendaraan-'.date('Y-m-d').'.pdf');
    }
}
