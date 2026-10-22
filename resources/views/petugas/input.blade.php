@extends('layouts.petugas')

@section('title', 'Formulir Pelaporan Kendaraan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Formulir Pelaporan Penertiban Kendaraan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Petugas: <strong class="text-slate-800">{{ auth()->user()->name }}</strong> ({{ '@' . auth()->user()->username }})</p>
            </div>
            <a href="{{ route('petugas.riwayat') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl transition shadow-2xs">
                Lihat Riwayat &rarr;
            </a>
        </div>

        @if ($errors->any())
            <div class="m-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <p class="font-bold mb-1">Terjadi kesalahan pada formulir:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('petugas.input.store') }}" method="POST" class="p-5 sm:p-6 space-y-6">
            @csrf

            <!-- 1. Identitas Pemilik / Pengemudi -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    1. Identitas Pemilik / Pengemudi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Pemilik / Pengemudi
                        </label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                               placeholder="Nama lengkap pengemudi / pemilik armada"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div>
                        <label for="no_sim" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nomor SIM
                        </label>
                        <input type="text" name="no_sim" id="no_sim" value="{{ old('no_sim') }}"
                               placeholder="Contoh: 1234-5678-901234" maxlength="50"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none uppercase text-slate-800">
                    </div>

                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat Pemilik / Pengemudi
                        </label>
                        <textarea name="alamat" id="alamat" rows="2"
                                  placeholder="Alamat domisili atau tempat tinggal lengkap..."
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 2. Informasi Kendaraan dan Trayek -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    2. Informasi Kendaraan dan Trayek
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="nomor_kendaraan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nomor Kendaraan / TNKB <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nomor_kendaraan" id="nomor_kendaraan" value="{{ old('nomor_kendaraan') }}"
                               placeholder="Contoh: F 1928 AB" required maxlength="20"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none uppercase font-bold text-slate-800">
                    </div>

                    <div>
                        <label for="jenis_kendaraan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Jenis Angkutan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="jenis_kendaraan" id="jenis_kendaraan" value="{{ old('jenis_kendaraan', 'Angkutan Kota (Angkot)') }}"
                               placeholder="Contoh: Angkutan Kota (Angkot)" required maxlength="100"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <!-- Input Teks Bebas Trayek Angkutan -->
                    <div>
                        <label for="nama_trayek" class="block text-xs font-semibold text-slate-700 mb-1">
                            Trayek Angkutan
                        </label>
                        <input type="text" name="nama_trayek" id="nama_trayek" value="{{ old('nama_trayek') }}"
                               placeholder="Contoh: Trayek 05" maxlength="150"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                        <p class="text-[10px] text-slate-400 mt-1">Ketik nama atau nomor trayek secara bebas.</p>
                    </div>

                    <!-- Tahun Pembuatan Kendaraan & Indikator Usia Otomatis -->
                    <div class="md:col-span-3"
                         x-data="{
                            tahun: '{{ old('tahun_kendaraan', 2002) }}',
                            currentYear: {{ date('Y') }},
                            get usia() {
                                let t = parseInt(this.tahun);
                                if (!t || isNaN(t) || t < 1970 || t > this.currentYear) return null;
                                return this.currentYear - t;
                            },
                            get isValid() {
                                let t = parseInt(this.tahun);
                                return t && !isNaN(t) && t >= 1970 && t <= this.currentYear;
                            },
                            get isMelanggar() {
                                return this.isValid && this.usia >= 20;
                            }
                         }">
                        <label for="tahun_kendaraan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tahun Pembuatan Kendaraan <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="number" name="tahun_kendaraan" id="tahun_kendaraan" x-model="tahun"
                                       min="1970" max="{{ date('Y') }}" required placeholder="Contoh: 2002"
                                       class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none font-bold text-slate-800">
                                <span class="text-xs font-medium text-slate-500 whitespace-nowrap">Tahun</span>
                            </div>

                            {{-- Indikator Status Otomatis --}}
                            <div>
                                <!-- Status Hijau: Usia < 20 Tahun -->
                                <template x-if="isValid && !isMelanggar">
                                    <div class="px-3 py-2 rounded-xl text-xs font-bold text-center border bg-emerald-100 text-emerald-800 border-emerald-200 flex items-center justify-center gap-1.5 shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>Layak Operasi (&lt; 20 Tahun)</span>
                                    </div>
                                </template>

                                <!-- Status Merah: Usia >= 20 Tahun -->
                                <template x-if="isValid && isMelanggar">
                                    <div class="px-3 py-2 rounded-xl text-xs font-bold text-center border bg-rose-100 text-rose-800 border-rose-200 flex items-center justify-center gap-1.5 shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        <span>Melanggar Batas (&ge; 20 Tahun)</span>
                                    </div>
                                </template>

                                <!-- Status Netral: Belum Valid / Kosong -->
                                <template x-if="!isValid">
                                    <div class="px-3 py-2 rounded-xl text-xs font-medium text-center border bg-slate-100 text-slate-500 border-slate-200 flex items-center justify-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Tahun Belum Valid</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Informasi Usia Operasional Otomatis -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-500 mt-1.5 gap-1">
                            <template x-if="isValid">
                                <p>Usia operasional armada: <span class="font-bold text-slate-800" x-text="usia + ' Tahun'"></span> (Dihitung: <span x-text="currentYear"></span> - <span x-text="tahun"></span>)</p>
                            </template>
                            <template x-if="!isValid">
                                <p class="text-slate-400">Masukkan tahun pembuatan (1970 s/d <span x-text="currentYear"></span>) untuk kalkulasi usia operasional otomatis.</p>
                            </template>
                            <p class="text-[10px] text-slate-400">Batas maksimal Perda Kota Bogor: 20 Tahun</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Pelanggaran dan Lokasi Pendataan -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    3. Pelanggaran dan Lokasi Pendataan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="jenis_pelanggaran" class="block text-xs font-semibold text-slate-700 mb-1">
                            Pilihan Jenis Pelanggaran <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_pelanggaran" id="jenis_pelanggaran" required
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none font-medium text-slate-800">
                            <option value="">-- Pilih Jenis Pelanggaran --</option>
                            <option value="pelanggaran_teknis" {{ old('jenis_pelanggaran', 'pelanggaran_teknis') === 'pelanggaran_teknis' ? 'selected' : '' }}>
                                Pelanggaran Teknis (Batas Usia Armada &ge; 20 Tahun / Kelaikan Fisik Armada)
                            </option>
                            <option value="pelanggaran_administrasi" {{ old('jenis_pelanggaran') === 'pelanggaran_administrasi' ? 'selected' : '' }}>
                                Pelanggaran Administrasi (Izin Trayek, Uji Berkala / KIR, KPS / Dokumen Operasional)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="lokasi_pelanggaran" class="block text-xs font-semibold text-slate-700 mb-1">
                            Lokasi Pelanggaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi_pelanggaran" id="lokasi_pelanggaran" value="{{ old('lokasi_pelanggaran') }}"
                               placeholder="Contoh: Jl. Pajajaran, Depan Terminal Baranangsiang" required maxlength="255"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div>
                        <label for="tanggal_pendataan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tanggal Pendataan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pendataan" id="tanggal_pendataan" value="{{ old('tanggal_pendataan', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>
                </div>
            </div>

            <!-- 4. Informasi Teknis Kendaraan (Opsional) -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    4. Spesifikasi Teknis Tambahan (Opsional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="merek" class="block text-xs font-semibold text-slate-700 mb-1">Merek</label>
                        <input type="text" name="merek" id="merek" value="{{ old('merek') }}" placeholder="Contoh: Suzuki / Daihatsu" maxlength="100"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div>
                        <label for="model_kendaraan" class="block text-xs font-semibold text-slate-700 mb-1">Model / Tipe</label>
                        <input type="text" name="model_kendaraan" id="model_kendaraan" value="{{ old('model_kendaraan') }}" placeholder="Contoh: Carry Futura 1.3" maxlength="100"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div>
                        <label for="warna" class="block text-xs font-semibold text-slate-700 mb-1">Warna Kendaraan</label>
                        <input type="text" name="warna" id="warna" value="{{ old('warna', 'Hijau / Putih') }}" maxlength="50"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div>
                        <label for="no_mesin" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Mesin</label>
                        <input type="text" name="no_mesin" id="no_mesin" value="{{ old('no_mesin') }}" placeholder="Nomor blok mesin" maxlength="50"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none uppercase text-slate-800">
                    </div>

                    <div>
                        <label for="no_rangka" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Rangka (VIN)</label>
                        <input type="text" name="no_rangka" id="no_rangka" value="{{ old('no_rangka') }}" placeholder="Nomor sasis/rangka" maxlength="50"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none uppercase text-slate-800">
                    </div>

                    <div class="md:col-span-3">
                        <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan Petugas</label>
                        <textarea name="catatan" id="catatan" rows="2" placeholder="Informasi khusus terkait penertiban armada di lokasi..."
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <button type="reset" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Reset Form
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Kirim Laporan Kendaraan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
