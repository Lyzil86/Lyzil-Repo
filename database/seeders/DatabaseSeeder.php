<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\BarangBukti;
use App\Models\Kendaraan;
use App\Models\PemilikPengemudi;
use App\Models\Penertib;
use App\Models\Trayek;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin & Petugas
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@dishub-bogor.go.id'],
            [
                'name' => 'Fazal Rachman, S.E.',
                'password' => Hash::make('admin123!'),
                'role' => 'superadmin',
                'is_active' => true,
                'last_login_at' => now(),
            ]
        );

        $operator = User::firstOrCreate(
            ['email' => 'petugas.dalops@dishub-bogor.go.id'],
            [
                'name' => 'Bambang Supriyanto',
                'password' => Hash::make('petugas123!'),
                'role' => 'operator',
                'is_active' => true,
                'last_login_at' => now()->subHours(3),
            ]
        );

        // 2. Trayek Angkutan Kota Bogor
        $trayekData = [
            ['kode' => '01', 'nama' => 'Trayek 01 (Baranangsiang - Bubulak via Otista)', 'asal' => 'Terminal Baranangsiang', 'tujuan' => 'Terminal Bubulak', 'deskripsi' => 'Rute: Baranangsiang - Otista - Juanda - Kapten Muslihat - Veteran - Bubulak'],
            ['kode' => '02', 'nama' => 'Trayek 02 (Baranangsiang - Ciparigi via Pajajaran)', 'asal' => 'Terminal Baranangsiang', 'tujuan' => 'Ciparigi', 'deskripsi' => 'Rute: Baranangsiang - Pajajaran - Warung Jambu - Ciparigi'],
            ['kode' => '03', 'nama' => 'Trayek 03 (Baranangsiang - Semplak)', 'asal' => 'Terminal Baranangsiang', 'tujuan' => 'Semplak / Salabenda', 'deskripsi' => 'Rute: Baranangsiang - Sudirman - Sawojajar - Semplak'],
            ['kode' => '04', 'nama' => 'Trayek 04 (Sukasari - Terminal Merdeka)', 'asal' => 'Sukasari', 'tujuan' => 'Terminal Merdeka', 'deskripsi' => 'Rute: Sukasari - Surya Kencana - Merdeka'],
            ['kode' => '05', 'nama' => 'Trayek 05 (Bubulak - Ciawi via Tol Baranangsiang)', 'asal' => 'Terminal Bubulak', 'tujuan' => 'Ciawi', 'deskripsi' => 'Rute: Bubulak - Yasmin - Tol Jagorawi - Ciawi'],
            ['kode' => '08', 'nama' => 'Trayek 08 (Ciparigi - Sukasari via Warung Jambu)', 'asal' => 'Ciparigi', 'tujuan' => 'Sukasari', 'deskripsi' => 'Rute: Ciparigi - Pajajaran - Sukasari'],
        ];

        $trayeks = [];
        foreach ($trayekData as $t) {
            $trayeks[$t['kode']] = Trayek::firstOrCreate(
                ['kode_trayek' => $t['kode']],
                [
                    'nama_trayek' => $t['nama'],
                    'asal' => $t['asal'],
                    'tujuan' => $t['tujuan'],
                    'deskripsi' => $t['deskripsi'],
                    'status' => 'aktif',
                ]
            );
        }

        // 3. Kendaraan Pelanggar Usia > 20 Tahun
        $armadaSample = [
            [
                'kode' => 'KND-2026-0001',
                'nopol' => 'F 1928 AB',
                'jenis' => 'Mobil Penumpang Umum (Angkot)',
                'trayek' => '01',
                'tahun' => 2001,
                'warna' => 'Hijau Muda - Putih',
                'merek' => 'Suzuki',
                'model' => 'Carry Futura 1.3',
                'no_mesin' => 'G13A-883712',
                'no_rangka' => 'MHESL413VJ-291823',
                'lokasi' => 'Jl. Pajajaran, Depan Terminal Baranangsiang',
                'tgl_data' => '2026-02-10',
                'verif' => 'diverifikasi',
                'penanganan' => 'selesai',
                'catatan' => 'Tertangkap operasi penertiban gabungan Dishub dan Satlantas. Unit telah melampaui usia teknis operasional 20 tahun.',
                'pemilik' => [
                    'peran' => 'pengemudi',
                    'nama' => 'Ahmad Hidayat',
                    'sim' => 'SIM-A-8712938471',
                    'ktp' => '3271011204800003',
                    'alamat' => 'Kampung Parung Jambu RT 02/RW 04, Bogor Timur',
                    'telepon' => '081298765431',
                ],
                'surat' => [
                    'nomor' => '500/ 160 - Daltib',
                    'tgl' => '2026-02-10',
                    'usia' => 25,
                    'bb' => [
                        ['jenis' => 'kir', 'nomor' => 'BGR-KIR-2019-0412', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'KIR mati sejak tahun 2021'],
                        ['jenis' => 'kps', 'nomor' => 'KPS-01/DISHUB/2018', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'KPS telah kedaluwarsa'],
                        ['jenis' => 'stnk', 'nomor' => 'STNK-09182736-F', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'STNK berlaku sampai 2023'],
                        ['jenis' => 'kendaraan', 'nomor' => 'F 1928 AB', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'Unit diamankan di Pool Dishub Cibuluh'],
                    ],
                ],
            ],
            [
                'kode' => 'KND-2026-0002',
                'nopol' => 'F 1812 CC',
                'jenis' => 'Mobil Penumpang Umum (Angkot)',
                'trayek' => '02',
                'tahun' => 2002,
                'warna' => 'Biru Tua - Putih',
                'merek' => 'Daihatsu',
                'model' => 'Zebra Espass 1.3',
                'no_mesin' => 'HC-9281729',
                'no_rangka' => 'MHKB1000VJ-192837',
                'lokasi' => 'Jl. KS Tubun, Warung Jambu, Bogor Utara',
                'tgl_data' => '2026-02-15',
                'verif' => 'diverifikasi',
                'penanganan' => 'sedang_diproses',
                'catatan' => 'Uji emisi gagal total dan usia armada 24 tahun.',
                'pemilik' => [
                    'peran' => 'pemilik_sekaligus_pengemudi',
                    'nama' => 'Syarifuddin Iskandar',
                    'sim' => 'SIM-A-9182736451',
                    'ktp' => '3271021406750002',
                    'alamat' => 'Jl. Ciparigi No. 18 RT 01/RW 03, Bogor Utara',
                    'telepon' => '085718273645',
                ],
                'surat' => [
                    'nomor' => '500/ 164 - Daltib',
                    'tgl' => '2026-02-15',
                    'usia' => 24,
                    'bb' => [
                        ['jenis' => 'kir', 'nomor' => 'BGR-KIR-2020-1102', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'Masa uji berkala telah habis'],
                        ['jenis' => 'kps', 'nomor' => 'KPS-02/DISHUB/2019', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'Kartu pengawasan ditarik'],
                        ['jenis' => 'stnk', 'nomor' => 'STNK-19283746-F', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'STNK asli disita'],
                        ['jenis' => 'kendaraan', 'nomor' => 'F 1812 CC', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'Fisik mobil disita di Kantor Dishub'],
                    ],
                ],
            ],
            [
                'kode' => 'KND-2026-0003',
                'nopol' => 'F 1904 KK',
                'jenis' => 'Mobil Penumpang Umum (Angkot)',
                'trayek' => '03',
                'tahun' => 2000,
                'warna' => 'Hijau Lumut - Kuning',
                'merek' => 'Suzuki',
                'model' => 'Carry ST 100',
                'no_mesin' => 'F10A-391823',
                'no_rangka' => 'MHESA410VJ-918273',
                'lokasi' => 'Jl. Abdullah Bin Nuh, Yasmin, Bogor Barat',
                'tgl_data' => '2026-02-22',
                'verif' => 'diverifikasi',
                'penanganan' => 'selesai',
                'catatan' => 'Sasis mengalami keretakan teknis dan usia 26 tahun.',
                'pemilik' => [
                    'peran' => 'pengemudi',
                    'nama' => 'Deden Rusli',
                    'sim' => 'SIM-A-7281920394',
                    'ktp' => '3271031809820004',
                    'alamat' => 'Kampung Semplak Pilar RT 03/RW 01, Bogor Barat',
                    'telepon' => '087819283746',
                ],
                'surat' => [
                    'nomor' => '500/ 172 - Daltib',
                    'tgl' => '2026-02-22',
                    'usia' => 26,
                    'bb' => [
                        ['jenis' => 'kir', 'nomor' => 'BGR-KIR-2018-0918', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'Buku KIR rusak fisik'],
                        ['jenis' => 'stnk', 'nomor' => 'STNK-92817263-F', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'Pajak tahunan nunggak 3 tahun'],
                        ['jenis' => 'kendaraan', 'nomor' => 'F 1904 KK', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'Kandang Dishub Cibuluh'],
                    ],
                ],
            ],
            [
                'kode' => 'KND-2026-0004',
                'nopol' => 'F 1977 MM',
                'jenis' => 'Mobil Penumpang Umum (Angkot)',
                'trayek' => '04',
                'tahun' => 2003,
                'warna' => 'Merah Hati - Putih',
                'merek' => 'Toyota',
                'model' => 'Kijang Super Kencana',
                'no_mesin' => '5K-8192837',
                'no_rangka' => 'MHKF5000VJ-192837',
                'lokasi' => 'Jl. Suryakencana, Bogor Tengah',
                'tgl_data' => '2026-03-01',
                'verif' => 'menunggu',
                'penanganan' => 'belum_ditangani',
                'catatan' => 'Usia 23 tahun, beroperasi tanpa izin trayek yang sah.',
                'pemilik' => [
                    'peran' => 'pemilik',
                    'nama' => 'H. Rahmat Suwandi',
                    'sim' => 'SIM-A-8192837465',
                    'ktp' => '3271041101680001',
                    'alamat' => 'Jl. Sukasari III No. 45, Bogor Selatan',
                    'telepon' => '081391827364',
                ],
                'surat' => null,
            ],
            [
                'kode' => 'KND-2026-0005',
                'nopol' => 'F 1955 ZZ',
                'jenis' => 'Mobil Penumpang Umum (Angkot)',
                'trayek' => '05',
                'tahun' => 1999,
                'warna' => 'Kuning - Hijau',
                'merek' => 'Suzuki',
                'model' => 'Carry 1.0',
                'no_mesin' => 'F10A-102938',
                'no_rangka' => 'MHESA410VJ-102938',
                'lokasi' => 'Jl. Raya Tajur, Depan Mall Boxies',
                'tgl_data' => '2026-03-05',
                'verif' => 'diverifikasi',
                'penanganan' => 'selesai',
                'catatan' => 'Usia armada 27 tahun. Dinyatakan afkir dan tidak boleh beroperasi lagi.',
                'pemilik' => [
                    'peran' => 'pengemudi',
                    'nama' => 'Joko Triyono',
                    'sim' => 'SIM-A-6152438910',
                    'ktp' => '3271052107850003',
                    'alamat' => 'Ciawi Girang RT 04/RW 02, Bogor Timur',
                    'telepon' => '081290192837',
                ],
                'surat' => [
                    'nomor' => '500/ 188 - Daltib',
                    'tgl' => '2026-03-05',
                    'usia' => 27,
                    'bb' => [
                        ['jenis' => 'kir', 'nomor' => 'BGR-KIR-2017-0021', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'Mati 7 tahun'],
                        ['jenis' => 'kps', 'nomor' => 'KPS-05/DISHUB/2017', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'KPS sobek dan kedaluwarsa'],
                        ['jenis' => 'stnk', 'nomor' => 'STNK-82910293-F', 'kondisi' => 'baik', 'status' => 'disita', 'ket' => 'STNK disita'],
                        ['jenis' => 'kendaraan', 'nomor' => 'F 1955 ZZ', 'kondisi' => 'rusak', 'status' => 'disita', 'ket' => 'Unit armada ditahan di Pool'],
                    ],
                ],
            ],
            [
                'kode' => 'KND-2026-0006',
                'nopol' => 'F 1988 AB',
                'jenis' => 'Mobil Penumpang Umum (Angkot)',
                'trayek' => '01',
                'tahun' => 2004,
                'warna' => 'Hijau Muda - Putih',
                'merek' => 'Suzuki',
                'model' => 'Carry Futura 1.5',
                'no_mesin' => 'G15A-918273',
                'no_rangka' => 'MHESL415VJ-091823',
                'lokasi' => 'Jl. Ir. H. Juanda, Samping Kantor Walikota',
                'tgl_data' => '2026-03-12',
                'verif' => 'menunggu',
                'penanganan' => 'sedang_diproses',
                'catatan' => 'Usia 22 tahun. Berkas dalam tahap pengujian berkala teknis.',
                'pemilik' => [
                    'peran' => 'pengemudi',
                    'nama' => 'Mulyadi',
                    'sim' => 'SIM-A-8172938472',
                    'ktp' => '3271010303880002',
                    'alamat' => 'Kebon Kelapa RT 01/RW 05, Bogor Tengah',
                    'telepon' => '085819283746',
                ],
                'surat' => null,
            ],
        ];

        foreach ($armadaSample as $item) {
            $trayekModel = isset($trayeks[$item['trayek']]) ? $trayeks[$item['trayek']] : null;
            $trayekId = $trayekModel ? $trayekModel->id : null;
            $trayekNama = $trayekModel ? ('Trayek '.$trayekModel->kode_trayek.' - '.$trayekModel->nama_trayek) : null;

            $k = Kendaraan::firstOrCreate(
                ['kode_data' => $item['kode']],
                [
                    'nomor_kendaraan' => $item['nopol'],
                    'jenis_kendaraan' => $item['jenis'],
                    'trayek_id' => $trayekId,
                    'nama_trayek' => $trayekNama,
                    'tahun_kendaraan' => $item['tahun'],
                    'jenis_pelanggaran' => 'pelanggaran_teknis',
                    'warna' => $item['warna'],
                    'merek' => $item['merek'],
                    'model_kendaraan' => $item['model'],
                    'no_mesin' => $item['no_mesin'],
                    'no_rangka' => $item['no_rangka'],
                    'lokasi_pelanggaran' => $item['lokasi'],
                    'tanggal_pendataan' => $item['tgl_data'],
                    'status_verifikasi' => $item['verif'],
                    'status_penanganan' => $item['penanganan'],
                    'catatan' => $item['catatan'],
                    'created_by' => $operator->id,
                    'verified_by' => $item['verif'] === 'diverifikasi' ? $superadmin->id : null,
                    'verified_at' => $item['verif'] === 'diverifikasi' ? now() : null,
                ]
            );

            // Pemilik / Pengemudi
            if (isset($item['pemilik'])) {
                PemilikPengemudi::firstOrCreate(
                    ['kendaraan_id' => $k->id, 'nama' => $item['pemilik']['nama']],
                    [
                        'peran' => $item['pemilik']['peran'],
                        'no_sim' => $item['pemilik']['sim'],
                        'no_ktp' => $item['pemilik']['ktp'],
                        'alamat' => $item['pemilik']['alamat'],
                        'no_telepon' => $item['pemilik']['telepon'],
                    ]
                );
            }

            // Surat Penyitaan & Barang Bukti
            if (isset($item['surat']) && $item['surat']) {
                $penertib = Penertib::firstOrCreate(
                    ['nomor_surat' => $item['surat']['nomor']],
                    [
                        'tanggal_surat' => $item['surat']['tgl'],
                        'kendaraan_id' => $k->id,
                        'lokasi_penertiban' => $item['lokasi'],
                        'tanggal_penertiban' => $item['surat']['tgl'],
                        'usia_teknis_operasional' => $item['surat']['usia'],
                        'petugas_penertiban' => 'FAZAL RACHMAN SE',
                        'status' => 'aktif',
                        'dasar_hukum' => 'Berdasarkan Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, Perda Kota Bogor Nomor 8 Tahun 2023 tentang Transportasi dan Perwali Kota Bogor Nomor 11 Tahun 2026 tentang Rasionalisasi, Peremajaan, dan Penghapusan Kendaraan Bermotor Umum dalam Trayek. Penyitaan dilakukan karena kendaraan tersebut secara teknis dan administrasi telah melanggar batas usia operasional armada angkutan umum yang ditetapkan oleh Pemerintah Daerah Kota Bogor yaitu maksimal 20 Tahun.',
                        'keterangan' => 'Penyitaan armada hasil operasi penegakan hukum batas usia angkutan.',
                        'created_by' => $superadmin->id,
                    ]
                );

                if (isset($item['surat']['bb'])) {
                    foreach ($item['surat']['bb'] as $bb) {
                        BarangBukti::firstOrCreate(
                            ['penertib_id' => $penertib->id, 'jenis' => $bb['jenis']],
                            [
                                'nomor_dokumen' => $bb['nomor'],
                                'kondisi' => $bb['kondisi'],
                                'status' => $bb['status'],
                                'keterangan' => $bb['ket'],
                            ]
                        );
                    }
                }
            }
        }

        // 4. Audit Log Awal
        AuditLog::create([
            'user_id' => $superadmin->id,
            'user_name' => $superadmin->name,
            'aksi' => 'login',
            'deskripsi' => 'Admin superadmin masuk ke dashboard sistem',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subHours(2),
        ]);

        AuditLog::create([
            'user_id' => $superadmin->id,
            'user_name' => $superadmin->name,
            'aksi' => 'create',
            'model' => 'Penertib',
            'model_id' => 1,
            'deskripsi' => 'Penerbitan Surat Penyitaan No. 500/ 160 - Daltib untuk F 1928 AB',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subHours(1),
        ]);
    }
}
