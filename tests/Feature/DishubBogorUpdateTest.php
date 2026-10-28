<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\Penertib;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DishubBogorUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Kop Surat Penyitaan menggunakan logo resmi Pemkot Bogor
     * dan Navbar memuat elemen identitas visual Kota Bogor & SIWASANG.
     */
    public function test_kop_surat_and_navbar_visual_identities(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();
        $penertib = Penertib::first();

        // 1. Kop surat penyitaan
        $cetakResponse = $this->actingAs($superadmin)->get(route('penertiban.cetak', $penertib));
        $cetakResponse->assertStatus(200);
        $cetakResponse->assertSee('images/logo-kota-bogor.png');
        $cetakResponse->assertSee('PEMERINTAH DAERAH KOTA BOGOR');
        $cetakResponse->assertSee('DINAS PERHUBUNGAN');

        // 2. Navbar dashboard
        $dashResponse = $this->actingAs($superadmin)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('images/logo-kota-bogor.png');
        $dashResponse->assertSee('images/logo-dishub.png');
        $dashResponse->assertSee('images/maskot-rubo.png');
        $dashResponse->assertSee('images/tugu-kujang.jpg');
        $dashResponse->assertSee('images/lawang-salapan.jpg');
        $dashResponse->assertSee('SIWASANG');
        $dashResponse->assertDontSee('Salawang');
    }

    /**
     * Test 2: Grafik Distribusi Pelanggaran Berdasarkan Jenis Pelanggaran
     * menampilkan data dinamis dari database.
     */
    public function test_dashboard_chart_distribusi_jenis_pelanggaran_is_dynamic(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();

        $response = $this->actingAs($superadmin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Distribusi Pelanggaran Berdasarkan Jenis Pelanggaran');
        $response->assertSee('chartJenisPelanggaran');
        $response->assertSee('Batas Usia Teknis');
        $response->assertSee('Pelanggaran Teknis Kendaraan');
        $response->assertSee('Administrasi Buku Uji (KIR)');
    }

    /**
     * Test 3: Input Trayek manual teks dan Perhitungan Tahun Pembuatan & Usia Kendaraan.
     */
    public function test_kendaraan_create_and_edit_with_manual_trayek_and_year_calculation(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();
        $currentYear = (int) date('Y');

        // 1. Create kendaraan dengan trayek teks manual & tahun pembuatan
        $createResponse = $this->actingAs($superadmin)->post(route('kendaraan.store'), [
            'nama' => 'Deden Kurniawan',
            'no_sim' => 'SIM-88776655',
            'alamat' => 'Jl. Pajajaran No. 45 Kota Bogor',
            'nomor_kendaraan' => 'F 8899 TST',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'nama_trayek' => 'Trayek 05 - Ciparigi',
            'tahun_kendaraan' => $currentYear - 24, // usia 24 tahun
            'jenis_pelanggaran' => 'pelanggaran_teknis',
            'lokasi_pelanggaran' => 'Sukasari, Bogor Timur',
            'tanggal_pendataan' => '2026-10-21',
            'status_verifikasi' => 'menunggu',
            'status_penanganan' => 'belum_ditangani',
            'warna' => 'Hijau / Putih',
            'merek' => 'Suzuki',
            'model_kendaraan' => 'Carry Futura 1.3',
        ]);

        $createResponse->assertRedirect();
        $kendaraan = Kendaraan::where('nomor_kendaraan', 'F 8899 TST')->first();
        $this->assertNotNull($kendaraan);
        $this->assertEquals('Trayek 05 - Ciparigi', $kendaraan->nama_trayek);
        $this->assertEquals($currentYear - 24, $kendaraan->tahun_kendaraan);
        $this->assertEquals(24, $kendaraan->usia_operasional);
        $this->assertTrue($kendaraan->is_melanggar_batas_usia); // 24 >= 20 -> melanggar

        // 2. Buka halaman Edit: pastikan semua field terisi kembali (pre-populated)
        $editPage = $this->actingAs($superadmin)->get(route('kendaraan.edit', $kendaraan));
        $editPage->assertStatus(200);
        $editPage->assertSee('Deden Kurniawan');
        $editPage->assertSee('SIM-88776655');
        $editPage->assertSee('Trayek 05 - Ciparigi');
        $editPage->assertSee('value="'.($currentYear - 24).'"', false);
        $editPage->assertSee('Carry Futura 1.3');

        // 3. Edit ubah tahun pembuatan (misal jadi 2022 -> usia 4 tahun, layak operasi) dan trayek
        $updateResponse = $this->actingAs($superadmin)->put(route('kendaraan.update', $kendaraan), [
            'nama' => 'Deden Kurniawan Updated',
            'no_sim' => 'SIM-88776655',
            'alamat' => 'Jl. Pajajaran No. 45 Kota Bogor',
            'nomor_kendaraan' => 'F 8899 TST',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'nama_trayek' => 'Trayek 02 - Sukasari Bubulak',
            'tahun_kendaraan' => $currentYear - 4, // usia 4 tahun
            'jenis_pelanggaran' => 'pelanggaran_administrasi',
            'lokasi_pelanggaran' => 'Terminal Baranangsiang',
            'tanggal_pendataan' => '2026-10-21',
            'status_verifikasi' => 'diverifikasi',
            'status_penanganan' => 'sedang_diproses',
            'warna' => 'Hijau / Putih',
            'merek' => 'Suzuki',
            'model_kendaraan' => 'Carry Futura 1.3',
        ]);

        $updateResponse->assertRedirect();
        $kendaraan->refresh();
        $this->assertEquals('Trayek 02 - Sukasari Bubulak', $kendaraan->nama_trayek);
        $this->assertEquals($currentYear - 4, $kendaraan->tahun_kendaraan);
        $this->assertEquals(4, $kendaraan->usia_operasional);
        $this->assertFalse($kendaraan->is_melanggar_batas_usia); // 4 < 20 -> layak
        $this->assertEquals('Deden Kurniawan Updated', $kendaraan->pemilikPengemudis->first()->nama);
    }

    /**
     * Test 4: Alur Registrasi Akun Petugas dan Persetujuan oleh Super Admin.
     */
    public function test_petugas_registration_and_superadmin_approval_workflow(): void
    {
        // 1. Registrasi publik petugas
        $regResponse = $this->post(route('register.post'), [
            'name' => 'Bripka Hendra Saputra',
            'username' => 'petugas_hendra',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $regResponse->assertRedirect(route('login'));
        $regResponse->assertSessionHas('success');

        $petugas = User::where('username', 'petugas_hendra')->first();
        $this->assertNotNull($petugas);
        $this->assertEquals('petugas', $petugas->role);
        $this->assertFalse($petugas->is_active);
        $this->assertEquals('menunggu_persetujuan', $petugas->status_akun);
        $this->assertTrue(Hash::check('Password123!', $petugas->password));

        // 2. Petugas mencoba login sebelum disetujui -> Ditolak
        $failedLogin = $this->post(route('login.post'), [
            'email' => 'petugas_hendra',
            'password' => 'Password123!',
        ]);
        $failedLogin->assertSessionHasErrors('email');
        $this->assertGuest();

        // 3. Super Admin melihat daftar petugas menunggu persetujuan
        $superadmin = User::where('role', 'superadmin')->first();
        $listResponse = $this->actingAs($superadmin)->get(route('admin.petugas.index'));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('petugas_hendra');
        $listResponse->assertSee('Menunggu Persetujuan');

        // 4. Super Admin menyetujui akun petugas
        $approveResponse = $this->actingAs($superadmin)->put(route('admin.petugas.toggle', $petugas), [
            'status' => 'aktif',
        ]);
        $approveResponse->assertRedirect();
        $petugas->refresh();
        $this->assertTrue($petugas->is_active);
        $this->assertEquals('aktif', $petugas->status_akun);

        // Logout superadmin agar sesi kembali menjadi guest
        $this->post(route('logout'));

        // 5. Petugas login setelah disetujui -> Otomatis diarahkan ke portal petugas
        $successLogin = $this->post(route('login.post'), [
            'email' => 'petugas_hendra',
            'password' => 'Password123!',
        ]);
        $successLogin->assertRedirect(route('petugas.input'));
        $this->assertAuthenticatedAs($petugas);
    }

    /**
     * Test 5: Pembatasan Hak Akses Petugas (Role Protection & 403 Forbidden).
     */
    public function test_petugas_cannot_access_administrative_routes(): void
    {
        $petugas = User::create([
            'name' => 'Petugas Lapangan 1',
            'username' => 'petugas_lapangan',
            'email' => 'lapangan@dishub-bogor.go.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'is_active' => true,
            'status_akun' => 'aktif',
        ]);

        // Coba akses Dashboard administratif -> Harus 403
        $dash = $this->actingAs($petugas)->get('/dashboard');
        $dash->assertStatus(403);

        // Coba akses Audit Log -> Harus 403
        $audit = $this->actingAs($petugas)->get('/audit-log');
        $audit->assertStatus(403);

        // Coba akses Manajemen Admin -> Harus 403
        $adminPage = $this->actingAs($petugas)->get('/admin');
        $adminPage->assertStatus(403);

        // Coba akses Manajemen Petugas oleh admin -> Harus 403
        $petugasAdmin = $this->actingAs($petugas)->get('/admin/petugas');
        $petugasAdmin->assertStatus(403);

        // Coba akses Data Trayek -> Harus 403
        $trayekPage = $this->actingAs($petugas)->get('/trayek');
        $trayekPage->assertStatus(403);

        // Coba akses Laporan -> Harus 403
        $laporanPage = $this->actingAs($petugas)->get('/laporan');
        $laporanPage->assertStatus(403);

        // Akses halaman portal petugas sendiri -> Harus 200 OK
        $petugasInput = $this->actingAs($petugas)->get(route('petugas.input'));
        $petugasInput->assertStatus(200);

        $petugasRiwayat = $this->actingAs($petugas)->get(route('petugas.riwayat'));
        $petugasRiwayat->assertStatus(200);

        $petugasProfil = $this->actingAs($petugas)->get(route('petugas.profil'));
        $petugasProfil->assertStatus(200);
    }

    /**
     * Test 6: Petugas melakukan input pelaporan kendaraan dan riwayat terisolasi.
     */
    public function test_petugas_input_and_isolated_riwayat(): void
    {
        $currentYear = (int) date('Y');

        $petugas1 = User::create([
            'name' => 'Petugas Satu',
            'username' => 'petugas1',
            'email' => 'p1@dishub.go.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'is_active' => true,
            'status_akun' => 'aktif',
        ]);

        $petugas2 = User::create([
            'name' => 'Petugas Dua',
            'username' => 'petugas2',
            'email' => 'p2@dishub.go.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'is_active' => true,
            'status_akun' => 'aktif',
        ]);

        // Petugas 1 input kendaraan
        $postResponse = $this->actingAs($petugas1)->post(route('petugas.input.store'), [
            'nama' => 'Supir Angkot 1',
            'no_sim' => 'SIM-11112222',
            'alamat' => 'Ciawi, Bogor',
            'nomor_kendaraan' => 'F 1122 CC',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'nama_trayek' => 'Trayek 01',
            'tahun_kendaraan' => $currentYear - 21,
            'jenis_pelanggaran' => 'pelanggaran_teknis',
            'lokasi_pelanggaran' => 'Simpang Ciawi',
            'tanggal_pendataan' => '2026-10-21',
        ]);

        $postResponse->assertRedirect(route('petugas.riwayat'));
        $kendaraanP1 = Kendaraan::where('nomor_kendaraan', 'F 1122 CC')->first();
        $this->assertNotNull($kendaraanP1);
        $this->assertEquals($petugas1->id, $kendaraanP1->created_by);

        // Petugas 1 melihat riwayatnya -> Muncul F 1122 CC
        $riwayatP1 = $this->actingAs($petugas1)->get(route('petugas.riwayat'));
        $riwayatP1->assertStatus(200);
        $riwayatP1->assertSee('F 1122 CC');

        // Petugas 2 melihat riwayatnya -> TIDAK boleh melihat F 1122 CC milik Petugas 1
        $riwayatP2 = $this->actingAs($petugas2)->get(route('petugas.riwayat'));
        $riwayatP2->assertStatus(200);
        $riwayatP2->assertDontSee('F 1122 CC');
    }

    /**
     * Test 7: Super Admin mereset password dan mengelola akun petugas.
     */
    public function test_superadmin_can_reset_password_and_manage_petugas(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();

        $petugas = User::create([
            'name' => 'Petugas Uji',
            'username' => 'petugas_uji',
            'email' => 'uji@dishub.go.id',
            'password' => Hash::make('oldpassword'),
            'role' => 'petugas',
            'is_active' => true,
            'status_akun' => 'aktif',
        ]);

        // Reset password oleh Super Admin
        $resetResponse = $this->actingAs($superadmin)->put(route('admin.petugas.reset-password', $petugas), [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);
        $resetResponse->assertRedirect();
        $petugas->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $petugas->password));

        // Update data petugas oleh Super Admin
        $updateResponse = $this->actingAs($superadmin)->put(route('admin.petugas.update', $petugas), [
            'name' => 'Petugas Uji Ganti Nama',
            'username' => 'petugas_uji_baru',
            'status_akun' => 'nonaktif',
        ]);
        $updateResponse->assertRedirect();
        $petugas->refresh();
        $this->assertEquals('Petugas Uji Ganti Nama', $petugas->name);
        $this->assertEquals('petugas_uji_baru', $petugas->username);
        $this->assertFalse($petugas->is_active);

        // Hapus akun petugas oleh Super Admin
        $delResponse = $this->actingAs($superadmin)->delete(route('admin.petugas.destroy', $petugas));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $petugas->id]);
    }

    /**
     * Test 8: Halaman login memuat identitas SIWASANG, logo Dishub di kanan, logo Pemkot, dan Rubo.
     */
    public function test_login_page_renders_siwasang_branding_and_logos(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // Title and branding
        $response->assertSee('SIWA');
        $response->assertSee('SANG');
        $response->assertSee('Sistem Informasi Pengawasan Angkutan');
        $response->assertDontSee('Salawang');
        $response->assertDontSee('Pendataan Penertiban Angkutan Umum Usia > 20 Tahun');

        // Official Logos
        $response->assertSee('images/logo-dishub.png');
        $response->assertSee('images/logo-kota-bogor.png');
        $response->assertSee('images/maskot-rubo.png');
    }

    /**
     * Test 9: Proses Logout POST sukses tanpa HTTP 500 untuk semua peran (Petugas, Admin, Superadmin).
     */
    public function test_logout_flow_for_petugas_admin_and_superadmin(): void
    {
        $roles = ['superadmin', 'admin', 'petugas'];

        foreach ($roles as $role) {
            $user = User::where('role', $role)->first();
            if (! $user) {
                $user = User::create([
                    'name' => 'User '.$role,
                    'username' => 'test_'.$role,
                    'email' => 'test_'.$role.'@dishub.go.id',
                    'password' => Hash::make('password123'),
                    'role' => $role,
                    'is_active' => true,
                    'status_akun' => 'aktif',
                ]);
            }

            $response = $this->actingAs($user)->post(route('logout'));
            $response->assertStatus(302);
            $response->assertRedirect(route('login'));
            $response->assertHeader('Cache-Control');
            $this->assertGuest();
        }
    }

    /**
     * Test 10: Petugas dapat Create dan Edit kendaraan di database kendaraan beserta bukti foto & koordinat GPS.
     */
    public function test_petugas_can_create_and_edit_kendaraan_with_photos_and_coordinates(): void
    {
        Storage::fake('public');

        $petugas = User::where('role', 'petugas')->first();
        if (! $petugas) {
            $petugas = User::create([
                'name' => 'Petugas Lapangan Uji',
                'username' => 'petugas_uji_lap',
                'email' => 'lapangan_uji@dishub.go.id',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'is_active' => true,
                'status_akun' => 'aktif',
            ]);
        }

        // 1. Akses halaman create
        $createGet = $this->actingAs($petugas)->get(route('kendaraan.create'));
        $createGet->assertStatus(200);

        // 2. Submit form create dengan foto & koordinat
        $file = UploadedFile::fake()->image('bukti_razia.jpg', 600, 400);

        $storeResponse = $this->actingAs($petugas)->post(route('kendaraan.store'), [
            'nama' => 'Bambang Sudirman',
            'no_sim' => 'SIM-99001122',
            'alamat' => 'Jl. Juanda No. 10 Kota Bogor',
            'nomor_kendaraan' => 'F 1234 BGR',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'nama_trayek' => 'Trayek 03 - Baranangsiang Bubulak',
            'tahun_kendaraan' => 2002,
            'jenis_pelanggaran' => 'pelanggaran_teknis',
            'lokasi_pelanggaran' => 'Depan BTM Jl. Juanda Bogor',
            'latitude' => -6.6012345,
            'longitude' => 106.7912345,
            'tanggal_pendataan' => '2026-10-27',
            'foto_bukti' => [$file],
        ]);

        $storeResponse->assertRedirect();

        $kendaraan = Kendaraan::where('nomor_kendaraan', 'F 1234 BGR')->first();
        $this->assertNotNull($kendaraan);
        $this->assertEquals(-6.6012345, (float) $kendaraan->latitude);
        $this->assertEquals(106.7912345, (float) $kendaraan->longitude);
        $this->assertEquals('menunggu', $kendaraan->status_verifikasi);
        $this->assertNotNull($kendaraan->foto_bukti);
        $this->assertCount(1, $kendaraan->foto_bukti);

        // Pastikan file tersimpan di storage
        Storage::disk('public')->assertExists($kendaraan->foto_bukti[0]);

        // 3. Akses halaman edit
        $editGet = $this->actingAs($petugas)->get(route('kendaraan.edit', $kendaraan));
        $editGet->assertStatus(200);
        $editGet->assertSee('F 1234 BGR');
        $editGet->assertSee('-6.6012345');

        // 4. Update form edit
        $updateResponse = $this->actingAs($petugas)->put(route('kendaraan.update', $kendaraan), [
            'nama' => 'Bambang Sudirman Updated',
            'nomor_kendaraan' => 'F 1234 BGR',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'nama_trayek' => 'Trayek 03 - Baranangsiang Bubulak',
            'tahun_kendaraan' => 2002,
            'jenis_pelanggaran' => 'pelanggaran_administrasi',
            'lokasi_pelanggaran' => 'Jl. Ir. H. Juanda No. 12',
            'latitude' => -6.6020000,
            'longitude' => 106.7920000,
            'tanggal_pendataan' => '2026-10-27',
        ]);

        $updateResponse->assertRedirect();
        $kendaraan->refresh();
        $this->assertEquals(-6.6020000, (float) $kendaraan->latitude);
        $this->assertEquals('Jl. Ir. H. Juanda No. 12', $kendaraan->lokasi_pelanggaran);
        $this->assertEquals('menunggu', $kendaraan->status_verifikasi); // Status verifikasi tetap 'menunggu' untuk petugas
    }

    /**
     * Test 11: Petugas tidak diperbolehkan menghapus kendaraan (403 Forbidden).
     */
    public function test_petugas_cannot_delete_kendaraan(): void
    {
        $petugas = User::where('role', 'petugas')->first();
        if (! $petugas) {
            $petugas = User::create([
                'name' => 'Petugas Lapangan',
                'username' => 'petugas_lap',
                'email' => 'petugas_lap@dishub.go.id',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'is_active' => true,
                'status_akun' => 'aktif',
            ]);
        }

        $kendaraan = Kendaraan::first();
        $this->assertNotNull($kendaraan);

        $deleteResponse = $this->actingAs($petugas)->delete(route('kendaraan.destroy', $kendaraan));
        $deleteResponse->assertStatus(403);
        $this->assertDatabaseHas('kendaraans', ['id' => $kendaraan->id]);
    }
}
