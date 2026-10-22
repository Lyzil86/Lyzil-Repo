<?php

namespace Tests\Feature;

use App\Models\BarangBukti;
use App\Models\Kendaraan;
use App\Models\Penertib;
use App\Models\Trayek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_from_root(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('DINAS PERHUBUNGAN');
        $response->assertSee('Kota Bogor');
        $response->assertDontSee('Daftar Akun Publik'); // Tidak ada registrasi publik
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::where('email', 'admin@dishub-bogor.go.id')->first();

        $response = $this->post('/login', [
            'email' => 'admin@dishub-bogor.go.id',
            'password' => 'admin123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_fails_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@dishub-bogor.go.id',
            'password' => 'passwordsalah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_protected_routes_redirect_guests_to_login(): void
    {
        $routes = ['/dashboard', '/kendaraan', '/penertiban', '/barang-bukti', '/trayek', '/laporan', '/audit-log'];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('login'));
        }
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $user = User::where('role', 'superadmin')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrasi');
        $response->assertSee('Total Kendaraan Terdata');
        $response->assertSee('Menunggu Verifikasi');
    }

    public function test_authenticated_admin_can_view_and_create_kendaraan(): void
    {
        $user = User::where('role', 'superadmin')->first();
        $trayek = Trayek::first();

        $response = $this->actingAs($user)->get('/kendaraan');
        $response->assertStatus(200);

        $createResponse = $this->actingAs($user)->post('/kendaraan', [
            'nomor_kendaraan' => 'F 9999 XX',
            'jenis_kendaraan' => 'Mobil Penumpang Umum',
            'trayek_id' => $trayek->id,
            'tahun_kendaraan' => 2000,
            'lokasi_pelanggaran' => 'Jl. Pajajaran, Bogor',
            'tanggal_pendataan' => date('Y-m-d'),
            'catatan' => 'Testing unit kendaraan',
        ]);

        $createResponse->assertRedirect();
        $this->assertDatabaseHas('kendaraans', [
            'nomor_kendaraan' => 'F 9999 XX',
            'tahun_kendaraan' => 2000,
        ]);
    }

    public function test_authenticated_admin_can_view_and_print_surat_penyitaan(): void
    {
        $user = User::where('role', 'superadmin')->first();
        $penertib = Penertib::first();

        $response = $this->actingAs($user)->get("/penertiban/{$penertib->id}");
        $response->assertStatus(200);
        $response->assertSee('Berkas Penertiban');

        $cetakResponse = $this->actingAs($user)->get("/penertiban/{$penertib->id}/cetak");
        $cetakResponse->assertStatus(200);
        $cetakResponse->assertSee('DINAS PERHUBUNGAN');
        $cetakResponse->assertSee('Buku Uji Berkala (KIR)');
        $cetakResponse->assertSee('TIDAK AKAN BEROPERASI KEMBALI');
    }

    public function test_laporan_export_csv(): void
    {
        $user = User::where('role', 'superadmin')->first();

        $response = $this->actingAs($user)->get('/laporan/export-csv');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_superadmin_can_manage_admin_users(): void
    {
        $superadmin = User::where('role', 'superadmin')->first();

        // Index
        $indexResponse = $this->actingAs($superadmin)->get(route('admin.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Manajemen Akun');

        // Create form
        $createResponse = $this->actingAs($superadmin)->get(route('admin.create'));
        $createResponse->assertStatus(200);

        // Store
        $storeResponse = $this->actingAs($superadmin)->post(route('admin.store'), [
            'name' => 'Admin Baru Dishub',
            'email' => 'admin.baru@dishub.go.id',
            'password' => 'Password123!',
            'role' => 'admin',
        ]);
        $storeResponse->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('users', ['email' => 'admin.baru@dishub.go.id']);

        $newUser = User::where('email', 'admin.baru@dishub.go.id')->first();

        // Edit form
        $editResponse = $this->actingAs($superadmin)->get(route('admin.edit', $newUser));
        $editResponse->assertStatus(200);

        // Update
        $updateResponse = $this->actingAs($superadmin)->put(route('admin.update', $newUser), [
            'name' => 'Admin Dishub Terupdate',
            'email' => 'admin.baru@dishub.go.id',
            'role' => 'operator',
            'is_active' => '1',
        ]);
        $updateResponse->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'name' => 'Admin Dishub Terupdate',
            'role' => 'operator',
        ]);

        // Destroy
        $deleteResponse = $this->actingAs($superadmin)->delete(route('admin.destroy', $newUser));
        $deleteResponse->assertRedirect(route('admin.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    public function test_non_superadmin_cannot_access_admin_management(): void
    {
        $regularAdmin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($regularAdmin)->get(route('admin.index'));
        $response->assertStatus(403);

        $postResponse = $this->actingAs($regularAdmin)->post(route('admin.store'), [
            'name' => 'Hacker Admin',
            'email' => 'hacker@dishub.go.id',
            'password' => 'Hacker123!',
            'role' => 'superadmin',
        ]);
        $postResponse->assertStatus(403);
    }

    public function test_barang_bukti_crud_and_delete_not_404(): void
    {
        $user = User::where('role', 'superadmin')->first();
        $penertib = Penertib::first();

        // Create Barang Bukti
        $createResponse = $this->actingAs($user)->post(route('barang-bukti.store'), [
            'penertib_id' => $penertib->id,
            'jenis' => 'kir',
            'nomor_dokumen' => 'KIR-TEST-2026',
            'kondisi' => 'baik',
            'status' => 'disita',
        ]);
        $createResponse->assertRedirect();
        $this->assertDatabaseHas('barang_buktis', [
            'nomor_dokumen' => 'KIR-TEST-2026',
        ]);

        $bb = BarangBukti::where('nomor_dokumen', 'KIR-TEST-2026')->first();

        // Edit form
        $editResponse = $this->actingAs($user)->get(route('barang-bukti.edit', $bb));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('KIR-TEST-2026');

        // Update
        $updateResponse = $this->actingAs($user)->put(route('barang-bukti.update', $bb), [
            'jenis' => 'kps',
            'nomor_dokumen' => 'KPS-TEST-UPDATED',
            'kondisi' => 'rusak',
            'status' => 'dikembalikan',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('barang_buktis', [
            'id' => $bb->id,
            'nomor_dokumen' => 'KPS-TEST-UPDATED',
            'jenis' => 'kps',
        ]);

        // Destroy - must not be 404
        $deleteResponse = $this->actingAs($user)->delete(route('barang-bukti.destroy', $bb));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('barang_buktis', [
            'id' => $bb->id,
        ]);
    }

    public function test_penertiban_view_edit_and_delete(): void
    {
        $user = User::where('role', 'superadmin')->first();
        $kendaraan = Kendaraan::first();

        // Buat Baru Penertiban
        $createResponse = $this->actingAs($user)->post(route('penertiban.store'), [
            'nomor_surat' => '500/TEST - Daltib',
            'tanggal_surat' => '2026-10-20',
            'kendaraan_id' => $kendaraan->id,
            'lokasi_penertiban' => 'Jl. Pajajaran Kota Bogor',
            'tanggal_penertiban' => '2026-10-20',
            'status' => 'draft',
        ]);
        $createResponse->assertRedirect();

        $penertib = Penertib::where('nomor_surat', '500/TEST - Daltib')->first();
        $this->assertNotNull($penertib);

        // Fitur Mata / Detail Penertiban
        $showResponse = $this->actingAs($user)->get(route('penertiban.show', $penertib));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('500/TEST - Daltib');

        // Fitur Edit
        $editResponse = $this->actingAs($user)->get(route('penertiban.edit', $penertib));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('500/TEST - Daltib');

        // Simpan Perubahan
        $updateResponse = $this->actingAs($user)->put(route('penertiban.update', $penertib), [
            'nomor_surat' => '500/TEST-EDITED - Daltib',
            'tanggal_surat' => '2026-10-20',
            'kendaraan_id' => $kendaraan->id,
            'lokasi_penertiban' => 'Jl. Juanda Kota Bogor',
            'tanggal_penertiban' => '2026-10-20',
            'status' => 'aktif',
        ]);
        $updateResponse->assertRedirect(route('penertiban.show', $penertib));
        $this->assertDatabaseHas('penertibs', [
            'id' => $penertib->id,
            'nomor_surat' => '500/TEST-EDITED - Daltib',
            'status' => 'aktif',
        ]);

        // Fitur Hapus
        $deleteResponse = $this->actingAs($user)->delete(route('penertiban.destroy', $penertib));
        $deleteResponse->assertRedirect(route('penertiban.index'));
        $this->assertSoftDeleted('penertibs', ['id' => $penertib->id]);
    }

    public function test_penertiban_and_barang_bukti_accessible_even_when_kendaraan_is_soft_deleted(): void
    {
        $user = User::where('role', 'superadmin')->first();
        $kendaraan = Kendaraan::first();

        // Buat penertiban dan barang bukti untuk kendaraan ini
        $penertib = Penertib::create([
            'nomor_surat' => '500/TRASH-TEST - Daltib',
            'tanggal_surat' => '2026-10-20',
            'kendaraan_id' => $kendaraan->id,
            'lokasi_penertiban' => 'Jl. Pajajaran Kota Bogor',
            'tanggal_penertiban' => '2026-10-20',
            'status' => 'aktif',
            'created_by' => $user->id,
        ]);

        $bb = BarangBukti::create([
            'penertib_id' => $penertib->id,
            'jenis' => 'kir',
            'nomor_dokumen' => 'KIR-TRASH-TEST',
            'kondisi' => 'baik',
            'status' => 'disita',
        ]);

        // Hapus (soft-delete) kendaraan
        $kendaraan->delete();
        $this->assertSoftDeleted('kendaraans', ['id' => $kendaraan->id]);

        // Halaman penertiban index tidak boleh crash dengan "Missing required parameter for [Route: kendaraan.show]"
        $penertibanIndex = $this->actingAs($user)->get(route('penertiban.index'));
        $penertibanIndex->assertStatus(200);

        // Halaman barang-bukti index tidak boleh crash
        $barangBuktiIndex = $this->actingAs($user)->get(route('barang-bukti.index'));
        $barangBuktiIndex->assertStatus(200);

        // Halaman detail penertiban tidak boleh crash
        $penertibanShow = $this->actingAs($user)->get(route('penertiban.show', $penertib));
        $penertibanShow->assertStatus(200);

        // Halaman cetak penertiban tidak boleh crash
        $penertibanCetak = $this->actingAs($user)->get(route('penertiban.cetak', $penertib));
        $penertibanCetak->assertStatus(200);
    }

    public function test_kendaraan_create_does_not_fail_unique_constraint_when_soft_deleted_records_exist(): void
    {
        $user = User::where('role', 'superadmin')->first();

        // Buat kendaraan dan hapus
        $k1 = Kendaraan::create([
            'kode_data' => 'KND-2026-0999',
            'nomor_kendaraan' => 'F 1111 AA',
            'jenis_kendaraan' => 'Angkot',
            'tahun_kendaraan' => 2001,
            'lokasi_pelanggaran' => 'Bogor',
            'tanggal_pendataan' => '2026-10-20',
            'created_by' => $user->id,
        ]);
        $k1->delete();

        // Buat kendaraan baru via HTTP request (simpan data kendaraan)
        $response = $this->actingAs($user)->post(route('kendaraan.store'), [
            'nomor_kendaraan' => 'F 2222 BB',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'tahun_kendaraan' => 2002,
            'lokasi_pelanggaran' => 'Jl. Pajajaran, Bogor',
            'tanggal_pendataan' => '2026-10-21',
        ]);

        $response->assertRedirect();
        $newKendaraan = Kendaraan::where('nomor_kendaraan', 'F 2222 BB')->first();
        $this->assertNotNull($newKendaraan);
        $this->assertEquals('KND-2026-1000', $newKendaraan->kode_data);
    }

    public function test_kendaraan_create_with_8_attributes_and_driver_info(): void
    {
        $user = User::where('role', 'superadmin')->first();
        $trayek = Trayek::first();

        // Cek halaman create dapat diakses dan memuat field yang diminta
        $createPage = $this->actingAs($user)->get(route('kendaraan.create'));
        $createPage->assertStatus(200);
        $createPage->assertSee('Registrasi Kendaraan Baru');
        $createPage->assertSee('name="nama"', false);
        $createPage->assertSee('name="no_sim"', false);
        $createPage->assertSee('name="alamat"', false);
        $createPage->assertSee('name="nomor_kendaraan"', false);
        $createPage->assertSee('name="nama_trayek"', false);
        $createPage->assertSee('name="tahun_kendaraan"', false);
        $createPage->assertSee('name="jenis_pelanggaran"', false);
        $createPage->assertSee('name="lokasi_pelanggaran"', false);

        // Submit form pendaftaran dengan 8 atribut
        $response = $this->actingAs($user)->post(route('kendaraan.store'), [
            'nama' => 'Ahmad Suhendar',
            'no_sim' => 'SIM-1234567890',
            'alamat' => 'Kampung Parung RT 02/04 Kota Bogor',
            'nomor_kendaraan' => 'F 8812 BB',
            'jenis_kendaraan' => 'Angkutan Kota (Angkot)',
            'nama_trayek' => 'Trayek 05',
            'tahun_kendaraan' => date('Y') - 22,
            'jenis_pelanggaran' => 'pelanggaran_teknis',
            'lokasi_pelanggaran' => 'Jl. Pajajaran, Sukasari',
            'tanggal_pendataan' => '2026-10-21',
        ]);

        $response->assertRedirect();
        $kendaraan = Kendaraan::where('nomor_kendaraan', 'F 8812 BB')->first();
        $this->assertNotNull($kendaraan);
        $this->assertEquals('pelanggaran_teknis', $kendaraan->jenis_pelanggaran);
        $this->assertEquals(22, $kendaraan->usia_operasional);
        $this->assertEquals(date('Y') - 22, $kendaraan->tahun_kendaraan);
        $this->assertEquals('Jl. Pajajaran, Sukasari', $kendaraan->lokasi_pelanggaran);

        // Pastikan relasi pemilikPengemudis tersimpan otomatis
        $this->assertDatabaseHas('pemilik_pengemudis', [
            'kendaraan_id' => $kendaraan->id,
            'nama' => 'Ahmad Suhendar',
            'no_sim' => 'SIM-1234567890',
            'alamat' => 'Kampung Parung RT 02/04 Kota Bogor',
        ]);

        // Cek halaman detail kendaraan menampilkan info ini
        $showPage = $this->actingAs($user)->get(route('kendaraan.show', $kendaraan));
        $showPage->assertStatus(200);
        $showPage->assertSee('Ahmad Suhendar');
        $showPage->assertSee('SIM-1234567890');
        $showPage->assertSee('Pelanggaran Teknis');
    }

    public function test_barang_bukti_optional_photo_upload_and_show_view(): void
    {
        Storage::fake('public');
        $user = User::where('role', 'superadmin')->first();
        $penertib = Penertib::first();

        $file = UploadedFile::fake()->image('stnk_bukti.jpg', 600, 400);

        // 1. Simpan barang bukti beserta foto opsional
        $storeResponse = $this->actingAs($user)->post(route('barang-bukti.store'), [
            'penertib_id' => $penertib->id,
            'jenis' => 'stnk',
            'nomor_dokumen' => 'STNK-IMG-999',
            'atas_nama' => 'Hendra',
            'kondisi' => 'baik',
            'status' => 'disita',
            'foto' => $file,
        ]);

        $storeResponse->assertRedirect(route('penertiban.show', $penertib->id));
        $bb = BarangBukti::where('nomor_dokumen', 'STNK-IMG-999')->first();
        $this->assertNotNull($bb);
        $this->assertNotNull($bb->foto);
        Storage::disk('public')->assertExists($bb->foto);

        // 2. Akses halaman view barang bukti
        $showResponse = $this->actingAs($user)->get(route('barang-bukti.show', $bb));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('STNK-IMG-999');
        $showResponse->assertSee($bb->foto_url);

        // 3. Akses halaman edit barang bukti
        $editResponse = $this->actingAs($user)->get(route('barang-bukti.edit', $bb));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Foto saat ini');

        // 4. Hapus foto saat update melalui opsi hapus_foto
        $oldPath = $bb->foto;
        $updateResponse = $this->actingAs($user)->put(route('barang-bukti.update', $bb), [
            'jenis' => 'stnk',
            'nomor_dokumen' => 'STNK-IMG-999-EDIT',
            'kondisi' => 'baik',
            'status' => 'disita',
            'hapus_foto' => '1',
        ]);

        $updateResponse->assertRedirect(route('penertiban.show', $penertib->id));
        $bb->refresh();
        $this->assertNull($bb->foto);
        Storage::disk('public')->assertMissing($oldPath);
    }
}
