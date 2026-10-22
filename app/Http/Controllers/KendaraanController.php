<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Kendaraan;
use App\Models\Trayek;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with(['trayek', 'createdBy'])
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('kode_data', 'like', "%{$request->search}%")
                    ->orWhere('nomor_kendaraan', 'like', "%{$request->search}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$request->search}%")
                    ->orWhere('lokasi_pelanggaran', 'like', "%{$request->search}%");
            }))
            ->when($request->trayek_id, fn ($q) => $q->where('trayek_id', $request->trayek_id))
            ->when($request->status_verifikasi, fn ($q) => $q->where('status_verifikasi', $request->status_verifikasi))
            ->when($request->status_penanganan, fn ($q) => $q->where('status_penanganan', $request->status_penanganan))
            ->when($request->tahun_kendaraan, fn ($q) => $q->where('tahun_kendaraan', $request->tahun_kendaraan));

        $sortCol = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['kode_data', 'nomor_kendaraan', 'tahun_kendaraan', 'tanggal_pendataan', 'status_verifikasi', 'created_at'];
        if (in_array($sortCol, $allowedSorts)) {
            $query->orderBy($sortCol, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $kendaraans = $query->paginate(15)->withQueryString();
        $trayeks = Trayek::where('status', 'aktif')->orderBy('kode_trayek')->get();

        return view('kendaraan.index', compact('kendaraans', 'trayeks'));
    }

    public function create()
    {
        $trayeks = Trayek::where('status', 'aktif')->orderBy('kode_trayek')->get();

        return view('kendaraan.create', compact('trayeks'));
    }

    public function store(Request $request)
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
            'trayek_id' => 'nullable|exists:trayeks,id',
            'tahun_kendaraan' => 'required|integer|min:1970|max:'.date('Y'),
            'usia_teknis_operasional' => 'nullable|integer|min:0|max:100',

            // Jenis Pelanggaran & Lokasi
            'jenis_pelanggaran' => 'nullable|in:pelanggaran_teknis,pelanggaran_administrasi',
            'lokasi_pelanggaran' => 'required|string|max:255',
            'tanggal_pendataan' => 'nullable|date',

            // Status & Fisik
            'status_verifikasi' => 'nullable|in:menunggu,diverifikasi,ditolak',
            'status_penanganan' => 'nullable|in:belum_ditangani,sedang_diproses,selesai,dibebaskan',
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
            'lokasi_pelanggaran.required' => 'Lokasi pelanggaran wajib diisi.',
        ]);

        $currentYear = (int) date('Y');
        $data['usia_operasional'] = $currentYear - (int) $data['tahun_kendaraan'];
        unset($data['usia_teknis_operasional']);

        $data['tanggal_pendataan'] = $request->tanggal_pendataan ?: date('Y-m-d');
        $data['kode_data'] = Kendaraan::generateKodeData();
        $data['created_by'] = auth()->id();

        // Pisahkan data pemilik/pengemudi sebelum insert kendaraan
        $namaPemilik = $data['nama'] ?? null;
        $noSim = $data['no_sim'] ?? null;
        $alamat = $data['alamat'] ?? null;
        unset($data['nama'], $data['no_sim'], $data['alamat']);

        $kendaraan = Kendaraan::create($data);

        // Simpan data pemilik/pengemudi jika diisi
        if ($namaPemilik || $noSim || $alamat) {
            $kendaraan->pemilikPengemudis()->create([
                'peran' => 'pemilik_sekaligus_pengemudi',
                'nama' => $namaPemilik ?: 'Pengemudi',
                'no_sim' => $noSim,
                'alamat' => $alamat,
            ]);
        }

        AuditLog::catat('create', "Menambah kendaraan {$kendaraan->nomor_kendaraan} ({$kendaraan->kode_data})", $kendaraan, [], $kendaraan->toArray());

        return redirect()->route('kendaraan.show', $kendaraan)
            ->with('success', "Kendaraan {$kendaraan->nomor_kendaraan} berhasil ditambahkan.");
    }

    public function show(Kendaraan $kendaraan)
    {
        $kendaraan->load(['trayek', 'pemilikPengemudis', 'penertibs.barangBuktis', 'createdBy', 'verifiedBy']);

        return view('kendaraan.show', compact('kendaraan'));
    }

    public function edit(Kendaraan $kendaraan)
    {
        $kendaraan->load(['trayek', 'pemilikPengemudis']);
        $trayeks = Trayek::where('status', 'aktif')->orderBy('kode_trayek')->get();

        return view('kendaraan.edit', compact('kendaraan', 'trayeks'));
    }

    public function update(Request $request, Kendaraan $kendaraan)
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
            'trayek_id' => 'nullable|exists:trayeks,id',
            'tahun_kendaraan' => 'required|integer|min:1970|max:'.date('Y'),

            // Identitas Fisik
            'warna' => 'nullable|string|max:50',
            'merek' => 'nullable|string|max:100',
            'model_kendaraan' => 'nullable|string|max:100',
            'no_mesin' => 'nullable|string|max:50',
            'no_rangka' => 'nullable|string|max:50',

            // Pelanggaran & Lokasi
            'jenis_pelanggaran' => 'nullable|in:pelanggaran_teknis,pelanggaran_administrasi',
            'lokasi_pelanggaran' => 'required|string|max:255',
            'tanggal_pendataan' => 'required|date',

            // Status & Catatan
            'status_verifikasi' => 'required|in:menunggu,diverifikasi,ditolak',
            'status_penanganan' => 'required|in:belum_ditangani,sedang_diproses,selesai,dibebaskan',
            'catatan' => 'nullable|string',
        ], [
            'nomor_kendaraan.required' => 'Nomor kendaraan wajib diisi.',
            'jenis_kendaraan.required' => 'Jenis kendaraan wajib diisi.',
            'tahun_kendaraan.required' => 'Tahun pembuatan kendaraan wajib diisi.',
            'lokasi_pelanggaran.required' => 'Lokasi pelanggaran wajib diisi.',
        ]);

        $currentYear = (int) date('Y');
        $data['usia_operasional'] = $currentYear - (int) $data['tahun_kendaraan'];

        if ($data['status_verifikasi'] === 'diverifikasi' && $kendaraan->status_verifikasi !== 'diverifikasi') {
            $data['verified_by'] = auth()->id();
            $data['verified_at'] = now();
        }

        // Pisahkan data pemilik/pengemudi
        $namaPemilik = $data['nama'] ?? null;
        $noSim = $data['no_sim'] ?? null;
        $alamat = $data['alamat'] ?? null;
        unset($data['nama'], $data['no_sim'], $data['alamat']);

        $dataLama = $kendaraan->toArray();
        $kendaraan->update($data);

        // Update relasi pemilik/pengemudi
        if ($namaPemilik || $noSim || $alamat) {
            $pp = $kendaraan->pemilikPengemudis()->first();
            if ($pp) {
                $pp->update([
                    'nama' => $namaPemilik ?: $pp->nama,
                    'no_sim' => $noSim !== null ? $noSim : $pp->no_sim,
                    'alamat' => $alamat !== null ? $alamat : $pp->alamat,
                ]);
            } else {
                $kendaraan->pemilikPengemudis()->create([
                    'peran' => 'pemilik_sekaligus_pengemudi',
                    'nama' => $namaPemilik ?: 'Pengemudi',
                    'no_sim' => $noSim,
                    'alamat' => $alamat,
                ]);
            }
        }

        AuditLog::catat('update', "Memperbarui kendaraan {$kendaraan->nomor_kendaraan}", $kendaraan, $dataLama, $kendaraan->fresh()->toArray());

        return redirect()->route('kendaraan.show', $kendaraan)
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        AuditLog::catat('delete', "Menghapus kendaraan {$kendaraan->nomor_kendaraan} ({$kendaraan->kode_data})", $kendaraan, $kendaraan->toArray());
        $kendaraan->delete();

        return redirect()->route('kendaraan.index')
            ->with('success', "Kendaraan {$kendaraan->nomor_kendaraan} berhasil dihapus.");
    }
}
