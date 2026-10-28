<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->json('foto_bukti')->nullable()->after('lokasi_pelanggaran');
            $table->decimal('latitude', 10, 8)->nullable()->after('foto_bukti');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->dropColumn(['foto_bukti', 'latitude', 'longitude']);
        });
    }
};
