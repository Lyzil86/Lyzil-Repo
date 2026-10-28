<?php

namespace App\Services;

use App\Models\Kendaraan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetService
{
    /**
     * Mengirimkan data laporan kendaraan baru ke Google Sheet webhook.
     */
    public function sync(Kendaraan $kendaraan): bool
    {
        $webhookUrl = config('services.google_sheet.webhook_url');

        if (empty($webhookUrl)) {
            return false;
        }

        try {
            $pemilik = $kendaraan->pemilikPengemudis()->first();
            $photoUrls = [];
            if (is_array($kendaraan->foto_bukti)) {
                foreach ($kendaraan->foto_bukti as $f) {
                    $photoUrls[] = asset('storage/'.ltrim($f, '/'));
                }
            }

            $payload = [
                'waktu' => now()->format('d/m/Y H:i'),
                'kode_data' => $kendaraan->kode_data,
                'nomor_kendaraan' => $kendaraan->nomor_kendaraan,
                'nama_pemilik' => $pemilik?->nama ?? '-',
                'no_sim' => $pemilik?->no_sim ?? '-',
                'jenis_kendaraan' => $kendaraan->jenis_kendaraan,
                'nama_trayek' => $kendaraan->nama_trayek ?: ($kendaraan->trayek?->nama_trayek ?? '-'),
                'tahun_kendaraan' => $kendaraan->tahun_kendaraan,
                'usia_operasional' => $kendaraan->usia_operasional,
                'status_batas_usia' => $kendaraan->is_melanggar_batas_usia ? 'MELANGGAR (>20 TH)' : 'LAIK OPERASI',
                'jenis_pelanggaran' => $kendaraan->jenis_pelanggaran_label,
                'lokasi_pelanggaran' => $kendaraan->lokasi_pelanggaran,
                'koordinat' => $kendaraan->koordinat_teks ?: '-',
                'link_foto' => ! empty($photoUrls) ? implode(', ', $photoUrls) : '-',
                'status_verifikasi' => ucfirst($kendaraan->status_verifikasi),
                'status_penanganan' => str_replace('_', ' ', ucfirst($kendaraan->status_penanganan)),
                'catatan' => $kendaraan->catatan ?: '-',
                'petugas' => $kendaraan->createdBy?->name ?? (auth()->user()?->name ?? 'Petugas'),
            ];

            $response = Http::timeout(5)->post($webhookUrl, $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('Google Sheet sync failed: '.$e->getMessage());

            return false;
        }
    }
}
