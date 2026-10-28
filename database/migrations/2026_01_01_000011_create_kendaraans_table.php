<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_data', 30)->unique();
            $table->string('nomor_kendaraan', 20);
            $table->string('jenis_kendaraan');
            $table->foreignId('trayek_id')->nullable()->constrained('trayeks')->nullOnDelete();
            $table->year('tahun_kendaraan');
            $table->integer('usia_operasional')->nullable();
            $table->string('warna')->nullable();
            $table->string('merek')->nullable();
            $table->string('model_kendaraan')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('lokasi_pelanggaran');
            $table->date('tanggal_pendataan');
            $table->enum('status_verifikasi', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu');
            $table->enum('status_penanganan', ['belum_ditangani', 'sedang_diproses', 'selesai', 'dibebaskan'])->default('belum_ditangani');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
