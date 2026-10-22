<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Trayek;
use Illuminate\Http\Request;

class TrayekController extends Controller
{
    public function index(Request $request)
    {
        $query = Trayek::withCount('kendaraans')
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('kode_trayek', 'like', "%{$request->search}%")
                    ->orWhere('nama_trayek', 'like', "%{$request->search}%")
                    ->orWhere('asal', 'like', "%{$request->search}%")
                    ->orWhere('tujuan', 'like', "%{$request->search}%");
            }))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderBy('kode_trayek');

        $trayeks = $query->paginate(15)->withQueryString();

        return view('trayek.index', compact('trayeks'));
    }

    public function create()
    {
        return view('trayek.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_trayek' => 'required|string|max:20|unique:trayeks,kode_trayek',
            'nama_trayek' => 'required|string|max:255',
            'asal' => 'nullable|string|max:255',
            'tujuan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif,diarsipkan',
        ], [
            'kode_trayek.required' => 'Kode trayek wajib diisi.',
            'kode_trayek.unique' => 'Kode trayek sudah digunakan.',
            'nama_trayek.required' => 'Nama trayek wajib diisi.',
        ]);

        $trayek = Trayek::create($data);
        AuditLog::catat('create', "Menambah trayek {$trayek->kode_trayek} - {$trayek->nama_trayek}", $trayek, [], $trayek->toArray());

        return redirect()->route('trayek.index')->with('success', "Trayek {$trayek->kode_trayek} berhasil ditambahkan.");
    }

    public function edit(Trayek $trayek)
    {
        return view('trayek.edit', compact('trayek'));
    }

    public function update(Request $request, Trayek $trayek)
    {
        $data = $request->validate([
            'kode_trayek' => 'required|string|max:20|unique:trayeks,kode_trayek,'.$trayek->id,
            'nama_trayek' => 'required|string|max:255',
            'asal' => 'nullable|string|max:255',
            'tujuan' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif,diarsipkan',
        ]);

        $dataLama = $trayek->toArray();
        $trayek->update($data);
        AuditLog::catat('update', "Memperbarui trayek {$trayek->kode_trayek}", $trayek, $dataLama, $trayek->fresh()->toArray());

        return redirect()->route('trayek.index')->with('success', "Trayek {$trayek->kode_trayek} berhasil diperbarui.");
    }

    public function destroy(Trayek $trayek)
    {
        if ($trayek->kendaraans()->count() > 0) {
            return redirect()->route('trayek.index')
                ->with('error', "Trayek {$trayek->kode_trayek} tidak dapat dihapus karena masih memiliki data kendaraan.");
        }

        AuditLog::catat('delete', "Menghapus trayek {$trayek->kode_trayek}", $trayek, $trayek->toArray());
        $trayek->delete();

        return redirect()->route('trayek.index')->with('success', "Trayek {$trayek->kode_trayek} berhasil dihapus.");
    }
}
