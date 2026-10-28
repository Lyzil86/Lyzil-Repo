@extends('layouts.admin')

@section('title', 'Registrasi Kendaraan Baru')
@section('page-title', 'Form Registrasi Kendaraan')
@section('breadcrumb')
    <span>Database</span> / <a href="{{ route('kendaraan.index') }}" class="hover:underline">Kendaraan</a> / <span class="text-slate-600 font-medium">Tambah Baru</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Registrasi Kendaraan Baru</h2>
                <p class="text-xs text-slate-500">Lengkapi formulir pendataan identitas pemilik/pengemudi, armada, tahun pembuatan, lokasi, dan status penanganan.</p>
            </div>
            <a href="{{ route('kendaraan.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 rounded-xl transition">
                &larr; Kembali
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

        <form action="{{ route('kendaraan.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6"
              x-data="{
                  jenisAngkutan: '{{ old('jenis_kendaraan', 'Angkutan Penumpang') }}',
                  namaTrayek: '{{ old('nama_trayek') }}',
                  lat: '{{ old('latitude') }}',
                  lng: '{{ old('longitude') }}',
                  gpsLoading: false,
                  gpsStatus: '',
                  getGPSLocation() {
                      if (!navigator.geolocation) {
                          alert('Browser Anda tidak mendukung geolokasi GPS.');
                          return;
                      }
                      this.gpsLoading = true;
                      this.gpsStatus = 'Mendeteksi titik koordinat GPS...';
                      navigator.geolocation.getCurrentPosition(
                          (pos) => {
                              this.lat = pos.coords.latitude.toFixed(7);
                              this.lng = pos.coords.longitude.toFixed(7);
                              this.gpsLoading = false;
                              this.gpsStatus = 'Lokasi GPS berhasil diambil (' + pos.coords.accuracy.toFixed(0) + 'm akurasi)';
                              if (window.updateMapMarker) {
                                  window.updateMapMarker(pos.coords.latitude, pos.coords.longitude);
                              }
                          },
                          (err) => {
                              this.gpsLoading = false;
                              this.gpsStatus = 'Gagal mengambil GPS: ' + err.message;
                          },
                          { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                      );
                  },
                  photos: [],
                  handlePhotoSelect(e) {
                      const files = Array.from(e.target.files);
                      if (this.photos.length + files.length > 5) {
                          alert('Maksimal 5 foto bukti laporan.');
                          return;
                      }
                      files.forEach(file => {
                          if (file.size > 5 * 1024 * 1024) {
                              alert('File ' + file.name + ' melebihi ukuran batas 5MB.');
                              return;
                          }
                          const reader = new FileReader();
                          reader.onload = (ev) => {
                              this.photos.push({
                                  name: file.name,
                                  size: (file.size / 1024).toFixed(0) + ' KB',
                                  url: ev.target.result,
                                  file: file
                              });
                          };
                          reader.readAsDataURL(file);
                      });
                  },
                  removePhoto(index) {
                      this.photos.splice(index, 1);
                  }
              }">
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
                               placeholder="Nama lengkap pemilik atau pengemudi armada"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div>
                        <label for="no_sim" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nomor Uji Kendaraan
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
                                  placeholder="Alamat domisili atau tempat tinggal pengemudi/pemilik..."
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
                        <select name="jenis_kendaraan" id="jenis_kendaraan" required
                                x-model="jenisAngkutan"
                                @change="if(jenisAngkutan === 'Angkutan Barang') { namaTrayek = ''; }"
                                class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800 font-medium">
                            <option value="">-- Pilih Jenis Angkutan --</option>
                            <option value="Angkutan Penumpang">
                                Angkutan Penumpang
                            </option>
                            <option value="Angkutan Barang">
                                Angkutan Barang
                            </option>
                        </select>
                    </div>

                    <!-- Input Teks Bebas Trayek Angkutan -->
                    <div>
                        <label for="nama_trayek" class="block text-xs font-semibold text-slate-700 mb-1">
                            Trayek Angkutan
                        </label>
                        <input type="text" name="nama_trayek" id="nama_trayek"
                               x-model="namaTrayek"
                               :disabled="jenisAngkutan === 'Angkutan Barang'"
                               :placeholder="jenisAngkutan === 'Angkutan Barang' ? 'Tidak berlaku untuk angkutan barang' : 'Contoh: Trayek 05'"
                               :class="jenisAngkutan === 'Angkutan Barang' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-800'"
                               maxlength="150"
                               class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none transition">
                        <p class="text-[10px] text-slate-400 mt-1" x-text="jenisAngkutan === 'Angkutan Barang' ? 'Trayek dinonaktifkan untuk angkutan barang.' : 'Ketik nama atau nomor trayek secara bebas.'"></p>
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
                                       value="{{ old('tahun_kendaraan') }}"
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
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        3. Pelanggaran dan Lokasi Pendataan
                    </h3>
                    <button type="button" @click="getGPSLocation()" :disabled="gpsLoading"
                            class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg text-xs font-semibold transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5" :class="{'animate-spin': gpsLoading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-text="gpsLoading ? 'Mendeteksi GPS...' : '📍 Ambil Koordinat GPS Saat Ini'"></span>
                    </button>
                </div>

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
                        <p class="text-[10px] text-slate-400 mt-1">Pilih kategori utama yang mendasari penindakan armada.</p>
                    </div>

                    <div>
                        <label for="tanggal_pendataan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tanggal Pendataan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pendataan" id="tanggal_pendataan" value="{{ old('tanggal_pendataan', date('Y-m-d')) }}" required
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                    </div>

                    <div class="md:col-span-2">
                        <label for="lokasi_pelanggaran" class="block text-xs font-semibold text-slate-700 mb-1">
                            Alamat / Lokasi Pelanggaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi_pelanggaran" id="lokasi_pelanggaran" value="{{ old('lokasi_pelanggaran') }}"
                               placeholder="Contoh: Jl. Pajajaran, Depan Terminal Baranangsiang Kota Bogor" required maxlength="255"
                               class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">
                        <p class="text-[10px] text-slate-400 mt-1">Titik ruas jalan atau lokasi pemeriksaan di Kota Bogor.</p>
                    </div>

                    <div>
                        <label for="latitude" class="block text-xs font-semibold text-slate-700 mb-1">
                            Titik Latitude (Lintang)
                        </label>
                        <input type="text" name="latitude" id="latitude" x-model="lat" readonly
                               placeholder="-6.597143"
                               class="w-full px-3 py-2 text-xs bg-slate-100 text-slate-600 border border-slate-200 rounded-xl focus:outline-none font-mono cursor-not-allowed">
                    </div>

                    <div>
                        <label for="longitude" class="block text-xs font-semibold text-slate-700 mb-1">
                            Titik Longitude (Bujur)
                        </label>
                        <input type="text" name="longitude" id="longitude" x-model="lng" readonly
                               placeholder="106.806039"
                               class="w-full px-3 py-2 text-xs bg-slate-100 text-slate-600 border border-slate-200 rounded-xl focus:outline-none font-mono cursor-not-allowed">
                    </div>

                    <template x-if="gpsStatus">
                        <div class="md:col-span-2 text-[11px] p-2 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="gpsStatus"></span>
                        </div>
                    </template>

                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-semibold text-slate-700">Peta Titik Lokasi:</span>
                            <span class="text-[10px] text-slate-400">Klik/geser pin peta untuk memperbarui koordinat</span>
                        </div>
                        <div id="mapContainer" class="w-full h-52 rounded-xl border border-slate-300 z-10 overflow-hidden bg-slate-100"></div>
                    </div>
                </div>
            </div>

            <!-- 4. Upload Bukti Foto Laporan -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        4. Upload Bukti Foto Laporan Petugas
                    </h3>
                    <span class="text-[10px] font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">Format: JPG, PNG, WEBP (Maks 5MB)</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pilih Satu atau Beberapa Foto Bukti (Armada, Plat Nomor, Pelanggaran)
                    </label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-blue-500 hover:bg-blue-50/30 transition relative cursor-pointer bg-white">
                        <input type="file" name="foto_bukti[]" id="foto_bukti" multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                               @change="handlePhotoSelect"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-1">
                            <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-xs font-semibold text-slate-700">Klik untuk memilih foto atau seret file ke sini</p>
                            <p class="text-[11px] text-slate-400">Dapat mengunggah hingga 5 gambar sekaligus</p>
                        </div>
                    </div>
                </div>

                <template x-if="photos.length > 0">
                    <div class="space-y-2 pt-2">
                        <p class="text-xs font-bold text-slate-700">Preview Foto yang Dipilih (<span x-text="photos.length"></span> foto):</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                            <template x-for="(photo, index) in photos" :key="index">
                                <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-white shadow-xs">
                                    <img :src="photo.url" class="w-full h-24 object-cover">
                                    <div class="p-1.5 text-[10px] text-slate-600 truncate bg-white" :title="photo.name">
                                        <p class="truncate font-medium" x-text="photo.name"></p>
                                        <p class="text-slate-400 text-[9px]" x-text="photo.size"></p>
                                    </div>
                                    <button type="button" @click="removePhoto(index)"
                                            class="absolute top-1 right-1 p-1 bg-rose-600 hover:bg-rose-700 text-white rounded-full shadow-md transition" title="Hapus foto ini">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- 5. Informasi Teknis Kendaraan -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    4. Informasi Teknis Kendaraan
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
                </div>
            </div>

            <!-- 5. Status Penanganan dan Validasi Berkas (Opsional) -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 space-y-4">
                <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    5. Status Penanganan dan Validasi Berkas (Opsional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(auth()->user()?->isPetugas())
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Status Verifikasi Berkas
                            </label>
                            <div class="py-2 px-3 bg-amber-50 border border-amber-200 rounded-xl text-xs font-bold text-amber-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>Menunggu Verifikasi (Otomatis ditinjau Admin)</span>
                            </div>
                            <input type="hidden" name="status_verifikasi" value="menunggu">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Status Penanganan Armada
                            </label>
                            <div class="py-2 px-3 bg-slate-100 border border-slate-200 rounded-xl text-xs font-medium text-slate-700">
                                Belum Ditangani (Baru Terdata)
                            </div>
                            <input type="hidden" name="status_penanganan" value="belum_ditangani">
                        </div>
                    @else
                        <div>
                            <label for="status_verifikasi" class="block text-xs font-semibold text-slate-700 mb-1">
                                Status Verifikasi Berkas <span class="text-rose-500">*</span>
                            </label>
                            <select name="status_verifikasi" id="status_verifikasi" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none font-semibold text-slate-800">
                                <option value="menunggu" {{ old('status_verifikasi', 'menunggu') === 'menunggu' ? 'selected' : '' }}>
                                    Menunggu Verifikasi
                                </option>
                                <option value="diverifikasi" {{ old('status_verifikasi') === 'diverifikasi' ? 'selected' : '' }}>
                                    Diverifikasi (Valid)
                                </option>
                                <option value="ditolak" {{ old('status_verifikasi') === 'ditolak' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="status_penanganan" class="block text-xs font-semibold text-slate-700 mb-1">
                                Status Penanganan Armada <span class="text-rose-500">*</span>
                            </label>
                            <select name="status_penanganan" id="status_penanganan" required
                                    class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none font-semibold text-slate-800">
                                <option value="belum_ditangani" {{ old('status_penanganan', 'belum_ditangani') === 'belum_ditangani' ? 'selected' : '' }}>
                                    Belum Ditangani
                                </option>
                                <option value="sedang_diproses" {{ old('status_penanganan') === 'sedang_diproses' ? 'selected' : '' }}>
                                    Sedang Diproses
                                </option>
                                <option value="selesai" {{ old('status_penanganan') === 'selesai' ? 'selected' : '' }}>
                                    Selesai Ditertibkan
                                </option>
                                <option value="dibebaskan" {{ old('status_penanganan') === 'dibebaskan' ? 'selected' : '' }}>
                                    Dibebaskan / Selesai Sidang
                                </option>
                            </select>
                        </div>
                    @endif

                    <div class="md:col-span-2">
                        <label for="catatan" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan Petugas</label>
                        <textarea name="catatan" id="catatan" rows="2" placeholder="Informasi khusus terkait kondisi fisik armada atau catatan penertiban di lapangan..."
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-slate-800">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('kendaraan.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Data Kendaraan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const defaultLat = parseFloat(document.getElementById('latitude')?.value) || -6.597143;
    const defaultLng = parseFloat(document.getElementById('longitude')?.value) || 106.806039;

    const mapElement = document.getElementById('mapContainer');
    if (!mapElement || typeof L === 'undefined') return;

    const map = L.map('mapContainer').setView([defaultLat, defaultLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoordinates(lat, lng) {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        if (latInput) {
            latInput.value = lat.toFixed(7);
            latInput.dispatchEvent(new Event('input'));
        }
        if (lngInput) {
            lngInput.value = lng.toFixed(7);
            lngInput.dispatchEvent(new Event('input'));
        }
    }

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    window.updateMapMarker = function(lat, lng) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 16);
    };
});
</script>
@endpush
