<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penertiban Angkutan Usia > 20 Tahun</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }
        .header h2 {
            margin: 2px 0;
            font-size: 13pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        .header p {
            margin: 0;
            font-size: 8pt;
            color: #475569;
        }
        .title-box {
            text-align: center;
            margin-bottom: 14px;
        }
        .title-box h1 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .title-box p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4.5px 6px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: monospace; }
        .badge-danger {
            color: #b91c1c;
            font-weight: bold;
        }
        .footer-table {
            width: 100%;
            border: none;
            margin-top: 20px;
        }
        .footer-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>PEMERINTAH DAERAH KOTA BOGOR</h3>
        <h2>DINAS PERHUBUNGAN</h2>
        <p>Jl. Raya Tajur No. 54 Telepon/Fax: (0251) 8333611 Bogor 16134</p>
    </div>

    <div class="title-box">
        <h1>LAPORAN PENDATAAN & PENERTIBAN ANGKUTAN UMUM USIA > 20 TAHUN</h1>
        <p>Berdasarkan Perda Kota Bogor No. 8/2023 & Perwali No. 11/2026 • Dicetak pada: {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 75px;">Kode Data</th>
                <th style="width: 75px;">No. Kendaraan</th>
                <th style="width: 70px;">Trayek</th>
                <th style="width: 45px;" class="text-center">Tahun</th>
                <th style="width: 50px;" class="text-center">Usia</th>
                <th>Lokasi Pelanggaran</th>
                <th style="width: 65px;" class="text-center">Tgl Pendataan</th>
                <th style="width: 65px;" class="text-center">Status Verifikasi</th>
                <th style="width: 70px;" class="text-center">Status Operasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kendaraans as $index => $k)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $k->kode_data }}</td>
                    <td class="font-bold">{{ $k->nomor_kendaraan }}</td>
                    <td>{{ $k->trayek?->kode_trayek ? 'Trayek ' . $k->trayek->kode_trayek : '-' }}</td>
                    <td class="text-center">{{ $k->tahun_kendaraan }}</td>
                    <td class="text-center badge-danger">{{ $k->usia_operasional }} Th</td>
                    <td>{{ $k->lokasi_pelanggaran }}</td>
                    <td class="text-center">{{ $k->tanggal_pendataan->format('d/m/Y') }}</td>
                    <td class="text-center">{{ ucfirst($k->status_verifikasi) }}</td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $k->status_penanganan)) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px;">Tidak ada data kendaraan ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td style="width: 60%;">
                <p style="margin: 0; font-size: 8pt; color: #64748b;">
                    Total Armada Terdata: <strong>{{ $kendaraans->count() }} unit</strong><br>
                    Dokumen ini dihasilkan secara otomatis dari Sistem Informasi Penertiban Angkutan Internal Dishub Kota Bogor.
                </p>
            </td>
            <td style="width: 40%; text-align: center;">
                Bogor, {{ now()->isoFormat('D MMMM Y') }}<br>
                <strong>Pejabat PPNS / Pengawas Angkutan</strong><br>
                DINAS PERHUBUNGAN KOTA BOGOR
                <div style="height: 45px;"></div>
                <strong><u>FAZAL RACHMAN SE</u></strong><br>
                NIP. ........................................
            </td>
        </tr>
    </table>
</body>
</html>
