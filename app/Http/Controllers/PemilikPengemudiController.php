<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kendaraan;
use App\Models\PemilikPengemudi;
use Illuminate\Http\Request;

class PemilikPengemudiController extends Controller
{
    public function create(Kendaraan $kendaraan)
    {
        return view('pemilik-pengemudi.create', compact('kendaraan'));
    }

    public function store(Request $request, Kendaraan $kendaraan)
    {
        $data = $request->validate([
            'peran' => 'required|in:pemilik,pengemudi,pemilik_sekaligus_pengemudi',
            'nama' => 'required|string|max:255',
            'no_sim' => 'nullable|string|max:30',
            'no_ktp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
        ], [
            'peran.required' => 'Peran wajib dipilih.',
            'nama.required' => 'Nama wajib diisi.',
        ]);

        $data['kendaraan_id'] = $kendaraan->id;
        $pp = PemilikPengemudi::create($data);

        AuditLog::catat('create', "Menambah data pemilik/pengemudi '{$pp->nama}' untuk kendaraan {$kendaraan->nomor_kendaraan}", $pp);

        return redirect()->route('kendaraan.show', $kendaraan)
            ->with('success', 'Data pemilik/pengemudi berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan, PemilikPengemudi $pemilikPengemudi)
    {
        return view('pemilik-pengemudi.edit', compact('kendaraan', 'pemilikPengemudi'));
    }

    public function update(Request $request, Kendaraan $kendaraan, PemilikPengemudi $pemilikPengemudi)
    {
        $data = $request->validate([
            'peran' => 'required|in:pemilik,pengemudi,pemilik_sekaligus_pengemudi',
            'nama' => 'required|string|max:255',
            'no_sim' => 'nullable|string|max:30',
            'no_ktp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $dataLama = $pemilikPengemudi->toArray();
        $pemilikPengemudi->update($data);
        AuditLog::catat('update', "Memperbarui data pemilik/pengemudi '{$pemilikPengemudi->nama}'", $pemilikPengemudi, $dataLama, $pemilikPengemudi->fresh()->toArray());

        return redirect()->route('kendaraan.show', $kendaraan)
            ->with('success', 'Data pemilik/pengemudi berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan, PemilikPengemudi $pemilikPengemudi)
    {
        AuditLog::catat('delete', "Menghapus data pemilik/pengemudi '{$pemilikPengemudi->nama}'", $pemilikPengemudi, $pemilikPengemudi->toArray());
        $pemilikPengemudi->delete();

        return redirect()->route('kendaraan.show', $kendaraan)
            ->with('success', 'Data pemilik/pengemudi berhasil dihapus.');
    }
}
