<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_buktis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penertib_id')->constrained('penertibs')->cascadeOnDelete();
            $table->enum('jenis', ['kir', 'kps', 'stnk', 'kendaraan', 'lainnya']);
            $table->string('nomor_dokumen')->nullable();
            $table->date('tanggal_dokumen')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->enum('kondisi', ['baik', 'rusak', 'tidak_ada'])->default('baik');
            $table->enum('status', ['disita', 'dikembalikan', 'dimusnahkan'])->default('disita');
            $table->date('tanggal_pengembalian')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_buktis');
    }
};
