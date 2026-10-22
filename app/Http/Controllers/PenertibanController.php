<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kendaraan;
use App\Models\Penertib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenertibanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penertib::with(['kendaraan.trayek', 'createdBy'])
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('nomor_surat', 'like', "%{$request->search}%")
                    ->orWhere('lokasi_penertiban', 'like', "%{$request->search}%")
                    ->orWhere('petugas_penertiban', 'like', "%{$request->search}%")
                    ->orWhereHas('kendaraan', fn ($q) => $q->where('nomor_kendaraan', 'like', "%{$request->search}%"));
            }))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->tanggal_dari, fn ($q) => $q->where('tanggal_penertiban', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->where('tanggal_penertiban', '<=', $request->tanggal_sampai))
            ->orderByDesc('tanggal_penertiban');

        $penertibs = $query->paginate(15)->withQueryString();

        return view('penertiban.index', compact('penertibs'));
    }

    public function create(Request $request)
    {
        $kendaraan = null;
        if ($request->kendaraan_id) {
            $kendaraan = Kendaraan::find($request->kendaraan_id);
        }
        $kendaraans = Kendaraan::orderBy('nomor_kendaraan')->get(['id', 'nomor_kendaraan', 'kode_data']);

        return view('penertiban.create', compact('kendaraan', 'kendaraans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_surat' => 'nullable|string|max:100',
            'tanggal_surat' => 'nullable|date',
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'lokasi_penertiban' => 'required|string|max:255',
            'tanggal_penertiban' => 'required|date',
            'usia_teknis_operasional' => 'nullable|integer|min:0|max:100',
            'petugas_penertiban' => 'nullable|string|max:255',
            'status' => 'required|in:draft,aktif,selesai,dibatalkan',
            'dasar_hukum' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan_internal' => 'nullable|string',
        ], [
            'kendaraan_id.required' => 'Kendaraan wajib dipilih.',
            'lokasi_penertiban.required' => 'Lokasi penertiban wajib diisi.',
            'tanggal_penertiban.required' => 'Tanggal penertiban wajib diisi.',
        ]);

        $data['created_by'] = auth()->id();
        $penertib = Penertib::create($data);

        AuditLog::catat('create', "Menambah data penertiban {$penertib->nomor_surat}", $penertib, [], $penertib->toArray());

        return redirect()->route('penertiban.show', $penertib)
            ->with('success', 'Data penertiban berhasil ditambahkan.');
    }

    public function show(Penertib $penertib)
    {
        $penertib->load(['kendaraan.trayek', 'kendaraan.pemilikPengemudis', 'barangBuktis', 'createdBy']);

        return view('penertiban.show', compact('penertib'));
    }

    public function cetak(Penertib $penertib)
    {
        $penertib->load(['kendaraan.trayek', 'kendaraan.pemilikPengemudis', 'barangBuktis', 'createdBy']);
        AuditLog::catat('export', "Mencetak Surat Penyitaan {$penertib->nomor_surat}", $penertib);

        return view('penertiban.cetak', compact('penertib'));
    }

    public function edit(Penertib $penertib)
    {
        $kendaraans = Kendaraan::orderBy('nomor_kendaraan')->get(['id', 'nomor_kendaraan', 'kode_data']);

        return view('penertiban.edit', compact('penertib', 'kendaraans'));
    }

    public function update(Request $request, Penertib $penertib)
    {
        $data = $request->validate([
            'nomor_surat' => 'nullable|string|max:100',
            'tanggal_surat' => 'nullable|date',
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'lokasi_penertiban' => 'required|string|max:255',
            'tanggal_penertiban' => 'required|date',
            'usia_teknis_operasional' => 'nullable|integer|min:0|max:100',
            'petugas_penertiban' => 'nullable|string|max:255',
            'status' => 'required|in:draft,aktif,selesai,dibatalkan',
            'dasar_hukum' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'catatan_internal' => 'nullable|string',
        ]);

        $dataLama = $penertib->toArray();
        $penertib->update($data);
        AuditLog::catat('update', "Memperbarui data penertiban {$penertib->nomor_surat}", $penertib, $dataLama, $penertib->fresh()->toArray());

        return redirect()->route('penertiban.show', $penertib)
            ->with('success', 'Data penertiban berhasil diperbarui.');
    }

    public function destroy(Penertib $penertib)
    {
        DB::transaction(function () use ($penertib) {
            $penertib->barangBuktis()->delete();
            AuditLog::catat('delete', "Menghapus data penertiban {$penertib->nomor_surat}", $penertib, $penertib->toArray());
            $penertib->delete();
        });

        return redirect()->route('penertiban.index')
            ->with('success', 'Data penertiban berhasil dihapus.');
    }
}
