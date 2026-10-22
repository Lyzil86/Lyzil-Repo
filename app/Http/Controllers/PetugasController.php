<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PetugasController extends Controller
{
    public function inputForm()
    {
        return view('petugas.input');
    }

    public function storeKendaraan(Request $request)
    {
        $data = $request->validate([
            // Identitas Pemilik / Pengemudi
            'nama' => 'nullable|string|max:255',
            'no_sim' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:500',

            // Data Kendaraan & Trayek
            'nomor_kendaraan' => 'required|string|max:20',
            'jenis_kendaraan' => 'required|string|max:100',
            'nama_trayek' => 'nullable|string|max:150',
            'tahun_kendaraan' => 'required|integer|min:1970|max:'.date('Y'),

            // Pelanggaran & Lokasi
            'jenis_pelanggaran' => 'required|in:pelanggaran_teknis,pelanggaran_administrasi',
            'lokasi_pelanggaran' => 'required|string|max:255',
            'tanggal_pendataan' => 'required|date',

            // Spesifikasi Fisik
            'warna' => 'nullable|string|max:50',
            'merek' => 'nullable|string|max:100',
            'model_kendaraan' => 'nullable|string|max:100',
            'no_mesin' => 'nullable|string|max:50',
            'no_rangka' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
        ], [
            'nomor_kendaraan.required' => 'Nomor kendaraan wajib diisi.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi.',
            'tahun_kendaraan.required' => 'Tahun pembuatan kendaraan wajib diisi.',
            'jenis_pelanggaran.required' => 'Pilihan jenis pelanggaran wajib dipilih.',
            'lokasi_pelanggaran.required' => 'Lokasi pelanggaran wajib diisi.',
            'tanggal_pendataan.required' => 'Tanggal pendataan wajib diisi.',
        ]);

        $currentYear = (int) date('Y');
        $data['usia_operasional'] = $currentYear - (int) $data['tahun_kendaraan'];
        $data['kode_data'] = Kendaraan::generateKodeData();
        $data['created_by'] = auth()->id();
        $data['status_verifikasi'] = 'menunggu';
        $data['status_penanganan'] = 'belum_ditangani';

        // Pisahkan data pemilik/pengemudi
        $namaPemilik = $data['nama'] ?? null;
        $noSim = $data['no_sim'] ?? null;
        $alamat = $data['alamat'] ?? null;
        unset($data['nama'], $data['no_sim'], $data['alamat']);

        $kendaraan = Kendaraan::create($data);

        if ($namaPemilik || $noSim || $alamat) {
            $kendaraan->pemilikPengemudis()->create([
                'peran' => 'pemilik_sekaligus_pengemudi',
                'nama' => $namaPemilik ?: 'Pengemudi',
                'no_sim' => $noSim,
                'alamat' => $alamat,
            ]);
        }

        AuditLog::catat(
            'create',
            'Petugas '.auth()->user()->name." melaporkan kendaraan {$kendaraan->nomor_kendaraan} ({$kendaraan->kode_data})",
            $kendaraan,
            [],
            $kendaraan->toArray()
        );

        return redirect()->route('petugas.riwayat')
            ->with('success', "Laporan penertiban kendaraan {$kendaraan->nomor_kendaraan} ({$kendaraan->kode_data}) berhasil disimpan dan dikirim.");
    }

    public function riwayat()
    {
        $kendaraans = Kendaraan::where('created_by', auth()->id())
            ->with(['pemilikPengemudis'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('petugas.riwayat', compact('kendaraans'));
    }

    public function profil()
    {
        $user = auth()->user();

        return view('petugas.profil', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::catat('update', "Petugas {$user->name} mengubah kata sandi mandiri");

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
