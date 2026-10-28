<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BarangBukti;
use App\Models\Penertib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangBuktiController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangBukti::with(['penertib.kendaraan'])
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('nomor_dokumen', 'like', "%{$request->search}%")
                    ->orWhereHas('penertib', fn ($q) => $q->where('nomor_surat', 'like', "%{$request->search}%"))
                    ->orWhereHas('penertib.kendaraan', fn ($q) => $q->where('nomor_kendaraan', 'like', "%{$request->search}%"));
            }))
            ->when($request->jenis, fn ($q) => $q->where('jenis', $request->jenis))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at');

        $barangBuktis = $query->paginate(15)->withQueryString();

        return view('barang-bukti.index', compact('barangBuktis'));
    }

    public function create(Request $request)
    {
        $penertib = null;
        if ($request->penertib_id) {
            $penertib = Penertib::with('kendaraan')->find($request->penertib_id);
        }
        $penertibs = Penertib::with('kendaraan')->orderByDesc('created_at')->get();

        return view('barang-bukti.create', compact('penertib', 'penertibs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'penertib_id' => 'required|exists:penertibs,id',
            'jenis' => 'required|in:kir,kps,stnk,kendaraan,lainnya',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date',
            'kondisi' => 'required|in:baik,rusak,tidak_ada',
            'status' => 'required|in:disita,dikembalikan,dimusnahkan',
            'tanggal_pengembalian' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'penertib_id.required' => 'Data penertiban wajib dipilih.',
            'jenis.required' => 'Jenis barang bukti wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang-bukti', 'public');
        }

        $bb = BarangBukti::create($data);
        AuditLog::catat('create', "Menambah barang bukti {$bb->jenis_label} untuk penertiban ID {$bb->penertib_id}", $bb, [], $bb->toArray());

        if ($request->has('from_index')) {
            return redirect()->route('barang-bukti.index')
                ->with('success', 'Barang bukti berhasil ditambahkan.');
        }

        return redirect()->route('penertiban.show', $bb->penertib_id)
            ->with('success', 'Barang bukti berhasil ditambahkan.');
    }

    public function show(BarangBukti $barangBukti)
    {
        $barangBukti->load(['penertib.kendaraan.trayek', 'penertib.kendaraan.pemilikPengemudis']);

        return view('barang-bukti.show', compact('barangBukti'));
    }

    public function edit(BarangBukti $barangBukti)
    {
        $barangBukti->load('penertib.kendaraan');

        return view('barang-bukti.edit', compact('barangBukti'));
    }

    public function update(Request $request, BarangBukti $barangBukti)
    {
        $data = $request->validate([
            'jenis' => 'required|in:kir,kps,stnk,kendaraan,lainnya',
            'nomor_dokumen' => 'nullable|string|max:100',
            'tanggal_dokumen' => 'nullable|date',
            'tanggal_kadaluarsa' => 'nullable|date',
            'kondisi' => 'required|in:baik,rusak,tidak_ada',
            'status' => 'required|in:disita,dikembalikan,dimusnahkan',
            'tanggal_pengembalian' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hapus_foto' => 'nullable|boolean',
        ], [
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        if ($request->boolean('hapus_foto')) {
            if ($barangBukti->foto && Storage::disk('public')->exists($barangBukti->foto)) {
                Storage::disk('public')->delete($barangBukti->foto);
            }
            $data['foto'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($barangBukti->foto && Storage::disk('public')->exists($barangBukti->foto)) {
                Storage::disk('public')->delete($barangBukti->foto);
            }
            $data['foto'] = $request->file('foto')->store('barang-bukti', 'public');
        }

        unset($data['hapus_foto']);

        $dataLama = $barangBukti->toArray();
        $barangBukti->update($data);
        AuditLog::catat('update', "Memperbarui barang bukti ID {$barangBukti->id}", $barangBukti, $dataLama, $barangBukti->fresh()->toArray());

        return redirect()->route('penertiban.show', $barangBukti->penertib_id)
            ->with('success', 'Barang bukti berhasil diperbarui.');
    }

    public function destroy(BarangBukti $barangBukti)
    {
        if ($barangBukti->foto && Storage::disk('public')->exists($barangBukti->foto)) {
            Storage::disk('public')->delete($barangBukti->foto);
        }

        AuditLog::catat('delete', "Menghapus barang bukti {$barangBukti->jenis_label}", $barangBukti, $barangBukti->toArray());
        $barangBukti->delete();

        return back()->with('success', 'Barang bukti berhasil dihapus.');
    }
}
