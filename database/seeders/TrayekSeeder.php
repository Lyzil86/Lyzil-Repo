<?php

namespace Database\Seeders;

use App\Models\Trayek;
use Illuminate\Database\Seeder;

class TrayekSeeder extends Seeder
{
    public function run(): void
    {
        $trayeks = [
            ['kode_trayek' => '01', 'nama_trayek' => 'Trayek 01', 'asal' => 'Terminal Baranangsiang', 'tujuan' => 'Bubulak', 'status' => 'aktif'],
            ['kode_trayek' => '02', 'nama_trayek' => 'Trayek 02', 'asal' => 'Terminal Baranangsiang', 'tujuan' => 'Ciparigi', 'status' => 'aktif'],
            ['kode_trayek' => '03', 'nama_trayek' => 'Trayek 03', 'asal' => 'Terminal Baranangsiang', 'tujuan' => 'Semplak', 'status' => 'aktif'],
            ['kode_trayek' => '04', 'nama_trayek' => 'Trayek 04', 'asal' => 'Sukasari', 'tujuan' => 'Merdeka', 'status' => 'aktif'],
            ['kode_trayek' => '05', 'nama_trayek' => 'Trayek 05', 'asal' => 'Bubulak', 'tujuan' => 'Ciawi', 'status' => 'aktif'],
        ];

        foreach ($trayeks as $trayek) {
            Trayek::firstOrCreate(['kode_trayek' => $trayek['kode_trayek']], $trayek);
        }
    }
}
