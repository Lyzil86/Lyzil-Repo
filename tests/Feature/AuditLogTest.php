<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_formatted_created_at_formats_correctly_to_wib_24h(): void
    {
        // Simulasi timestamp UTC di database: 2026-10-20 04:34:26 UTC (+00:00)
        $log = AuditLog::create([
            'user_name' => 'Admin Dili Muryanto, SE., M.Si',
            'aksi' => 'login',
            'deskripsi' => 'Admin Dili Muryanto, SE., M.Si berhasil login',
            'created_at' => Carbon::parse('2026-10-20 04:34:26', 'UTC'),
        ]);

        // Harus dikonversi ke Asia/Jakarta (+07:00) => 20/10/2026 11:34:26 WIB
        $this->assertEquals('20/10/2026 11:34:26 WIB', $log->formatted_created_at);
        $this->assertEquals('20/10/2026 11:34:26', $log->created_at_wib?->format('d/m/Y H:i:s'));
    }

    public function test_audit_log_formatted_created_at_handles_null_gracefully(): void
    {
        $log = new AuditLog;
        $log->created_at = null;

        $this->assertEquals('-', $log->formatted_created_at);
        $this->assertNull($log->created_at_wib);
    }

    public function test_audit_log_catat_records_activity_and_formats_wib(): void
    {
        $user = User::factory()->create([
            'name' => 'Petugas Dishub',
            'email' => 'petugas@dishub.test',
            'role' => 'admin',
        ]);

        $this->actingAs($user);

        AuditLog::catat('create', 'Uji coba pencatatan audit log');

        $log = AuditLog::where('aksi', 'create')->first();

        $this->assertNotNull($log);
        $this->assertEquals('Petugas Dishub', $log->user_name);
        $this->assertStringEndsWith('WIB', $log->formatted_created_at);
        // Pastikan format DD/MM/YYYY HH:mm:ss WIB (24 jam)
        $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2} WIB$/', $log->formatted_created_at);
    }

    public function test_audit_log_page_displays_formatted_date_in_wib(): void
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@dishub.test',
            'role' => 'superadmin',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'aksi' => 'login',
            'deskripsi' => 'Admin Dili Muryanto, SE., M.Si berhasil login',
            'ip_address' => '127.0.0.1',
            'created_at' => Carbon::parse('2026-10-20 04:34:26', 'UTC'),
        ]);

        $response = $this->actingAs($user)->get(route('audit-log.index'));

        $response->assertStatus(200);
        $response->assertSee('20/10/2026 11:34:26 WIB');
        $response->assertSee('Waktu Log (WIB)');
        $response->assertSee('Detail Aktivitas Petugas');
    }

    public function test_audit_log_filter_by_wib_date(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
        ]);

        // Dibuat pada 20 Oktober 2026 jam 01:00 WIB (UTC: 2026-10-19 18:00:00)
        $log1 = AuditLog::create([
            'user_name' => 'Admin A',
            'aksi' => 'login',
            'deskripsi' => 'Aktivitas 20 Okt Pagi',
            'created_at' => Carbon::parse('2026-10-19 18:00:00', 'UTC'),
        ]);

        // Dibuat pada 19 Oktober 2026 jam 22:00 WIB (UTC: 2026-10-19 15:00:00)
        $log2 = AuditLog::create([
            'user_name' => 'Admin B',
            'aksi' => 'login',
            'deskripsi' => 'Aktivitas 19 Okt Malam',
            'created_at' => Carbon::parse('2026-10-19 15:00:00', 'UTC'),
        ]);

        // Filter tanggal 20 Oktober 2026
        $response = $this->actingAs($user)->get(route('audit-log.index', [
            'tanggal_dari' => '2026-10-20',
            'tanggal_sampai' => '2026-10-20',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Aktivitas 20 Okt Pagi');
        $response->assertDontSee('Aktivitas 19 Okt Malam');
    }
}
