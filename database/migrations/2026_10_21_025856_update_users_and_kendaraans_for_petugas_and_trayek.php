<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
            $table->string('status_akun', 30)->default('aktif')->after('is_active');
        });

        // Backfill username untuk user yang sudah ada berdasarkan email/name
        $existingUsers = DB::table('users')->get();
        foreach ($existingUsers as $u) {
            $baseUsername = strtolower(explode('@', $u->email)[0]);
            $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername) ?: 'user'.$u->id;
            DB::table('users')->where('id', $u->id)->update([
                'username' => $baseUsername,
                'status_akun' => 'aktif',
            ]);
        }

        Schema::table('kendaraans', function (Blueprint $table) {
            $table->string('nama_trayek', 150)->nullable()->after('trayek_id');
        });

        // Backfill nama_trayek dari relasi trayek_id yang sudah ada agar tidak hilang
        $kendaraans = DB::table('kendaraans')->whereNotNull('trayek_id')->get();
        foreach ($kendaraans as $k) {
            $trayek = DB::table('trayeks')->where('id', $k->trayek_id)->first();
            if ($trayek) {
                $label = 'Trayek '.$trayek->kode_trayek.' - '.$trayek->nama_trayek;
                DB::table('kendaraans')->where('id', $k->id)->update([
                    'nama_trayek' => $label,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->dropColumn('nama_trayek');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'status_akun']);
        });
    }
};
