@extends('layouts.admin')

@section('title', 'Edit Pemilik / Pengemudi')
@section('page-title', 'Edit Data Pemilik / Pengemudi')
@section('breadcrumb')
    <span>Database</span> / <a href="{{ route('kendaraan.show', $kendaraan) }}" class="hover:underline">{{ $kendaraan->nomor_kendaraan }}</a> / <span class="text-slate-600 font-medium">Edit Identitas</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Edit Identitas: {{ $pemilikPengemudi->nama }}</h2>
                <p class="text-xs text-slate-500">Kendaraan: <strong class="text-slate-800">{{ $kendaraan->nomor_kendaraan }}</strong></p>
            </div>
            <a href="{{ route('kendaraan.show', $kendaraan) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl">
                &larr; Batal
            </a>
        </div>

        <form action="{{ route('pemilik-pengemudi.update', [$kendaraan, $pemilikPengemudi]) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="peran" class="block text-xs font-semibold text-slate-700 mb-1">Peran <span class="text-rose-500">*</span></label>
                <select name="peran" id="peran" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-medium">
                    <option value="pengemudi" {{ old('peran', $pemilikPengemudi->peran) === 'pengemudi' ? 'selected' : '' }}>Pengemudi / Supir</option>
                    <option value="pemilik" {{ old('peran', $pemilikPengemudi->peran) === 'pemilik' ? 'selected' : '' }}>Pemilik Armada</option>
                    <option value="pemilik_sekaligus_pengemudi" {{ old('peran', $pemilikPengemudi->peran) === 'pemilik_sekaligus_pengemudi' ? 'selected' : '' }}>Pemilik Sekaligus Pengemudi</option>
                </select>
            </div>

            <div>
                <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $pemilikPengemudi->nama) }}" required
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="no_sim" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Uji Kendaraan</label>
                    <input type="text" name="no_sim" id="no_sim" value="{{ old('no_sim', $pemilikPengemudi->no_sim) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono">
                </div>
                <div>
                    <label for="no_ktp" class="block text-xs font-semibold text-slate-700 mb-1">Nomor KTP / NIK</label>
                    <input type="text" name="no_ktp" id="no_ktp" value="{{ old('no_ktp', $pemilikPengemudi->no_ktp) }}"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono">
                </div>
            </div>

            <div>
                <label for="no_telepon" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $pemilikPengemudi->no_telepon) }}"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-mono">
            </div>

            <div>
                <label for="alamat" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                <textarea name="alamat" id="alamat" rows="3"
                          class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('alamat', $pemilikPengemudi->alamat) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('kendaraan.show', $kendaraan) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
