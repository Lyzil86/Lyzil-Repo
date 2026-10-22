<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trayeks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_trayek', 20)->unique();
            $table->string('nama_trayek');
            $table->string('asal')->nullable();
            $table->string('tujuan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'diarsipkan'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trayeks');
    }
};
