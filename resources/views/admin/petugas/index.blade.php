@extends('layouts.admin')

@section('title', 'Pengelolaan Akun Petugas')
@section('page-title', 'Pengelolaan Akun Petugas Pelapor')
@section('breadcrumb')
    <span>Administrasi</span> / <a href="{{ route('admin.index') }}" class="hover:underline">Manajemen Admin</a> / <span class="text-slate-600 font-medium">Petugas Lapangan</span>
@endsection

@section('content')
<div class="space-y-5" x-data="{
    editModal: false,
    editUrl: '',
    editData: { id: '', name: '', username: '', status_akun: 'aktif' },
    resetModal: false,
    resetUrl: '',
    resetUser: '',
    deleteModal: false,
    deleteUrl: '',
    deleteItemName: ''
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-800">Daftar Akun Petugas Pelapor</h2>
                @if($countMenunggu > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                        {{ $countMenunggu }} Menunggu Persetujuan
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Kelola verifikasi pendaftaran, status aktivasi, dan kredensial akun petugas lapangan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.index') }}" class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-xs transition">
                &larr; Kelola Admin Sistem
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
            <a href="{{ route('admin.petugas.index') }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ empty($statusFilter) ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.petugas.index', ['status' => 'menunggu_persetujuan']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 {{ $statusFilter === 'menunggu_persetujuan' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Menunggu Persetujuan
                @if($countMenunggu > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $statusFilter === 'menunggu_persetujuan' ? 'bg-white text-amber-800 font-bold' : 'bg-amber-200 text-amber-900 font-bold' }}">
                        {{ $countMenunggu }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.petugas.index', ['status' => 'aktif']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $statusFilter === 'aktif' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                Aktif
            </a>
            <a href="{{ route('admin.petugas.index', ['status' => 'nonaktif']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition {{ $statusFilter === 'nonaktif' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                Dinonaktifkan
            </a>
        </div>

        <form method="GET" action="{{ route('admin.petugas.index') }}" class="flex items-center gap-2">
            @if($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau username..."
                       class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
            </div>
            <button type="submit" class="py-1.5 px-3.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Nama Petugas</th>
                        <th class="px-4 py-3.5">Username</th>
                        <th class="px-4 py-3.5">Peran / Role</th>
                        <th class="px-4 py-3.5">Status Akun</th>
                        <th class="px-4 py-3.5">Tgl Pendaftaran</th>
                        <th class="px-4 py-3.5 text-right">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($petugasList as $petugas)
                        <tr class="hover:bg-slate-50/80 transition {{ $petugas->status_akun === 'menunggu_persetujuan' ? 'bg-amber-50/30' : '' }}">
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 block">{{ $petugas->name }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $petugas->email }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-blue-700">
                                {{ '@' . ($petugas->username ?: 'belum_diatur') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800">
                                    {{ $petugas->role_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($petugas->status_akun === 'menunggu_persetujuan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                        Menunggu Persetujuan
                                    </span>
                                @elseif($petugas->status_akun === 'aktif' && $petugas->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Dinonaktifkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ $petugas->created_at->isoFormat('D MMM Y, HH:mm') }} WIB
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Aksi Persetujuan / Aktivasi Cepat -->
                                    @if($petugas->status_akun === 'menunggu_persetujuan')
                                        <form action="{{ route('admin.petugas.toggle', $petugas) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="aktif">
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-xs cursor-pointer" title="Setujui dan Aktifkan Akun">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Setujui
                                            </button>
                                        </form>
                                    @elseif($petugas->is_active)
                                        <form action="{{ route('admin.petugas.toggle', $petugas) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-2 py-1 bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-700 rounded-lg text-[11px] font-semibold transition cursor-pointer" title="Nonaktifkan Akun">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.petugas.toggle', $petugas) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="aktif">
                                            <button type="submit" class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[11px] font-semibold transition cursor-pointer" title="Aktifkan Akun">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Edit Data -->
                                    <button type="button"
                                            @click="editModal = true; editUrl = '{{ route('admin.petugas.update', $petugas) }}'; editData = { id: '{{ $petugas->id }}', name: '{{ addslashes($petugas->name) }}', username: '{{ $petugas->username }}', status_akun: '{{ $petugas->status_akun }}' }"
                                            class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit Data Petugas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Tombol Reset Password -->
                                    <button type="button"
                                            @click="resetModal = true; resetUrl = '{{ route('admin.petugas.reset-password', $petugas) }}'; resetUser = '{{ addslashes($petugas->name) }} ({{ $petugas->username }})'"
                                            class="p-1.5 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Atur Ulang Kata Sandi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <button type="button"
                                            @click="deleteModal = true; deleteUrl = '{{ route('admin.petugas.destroy', $petugas) }}'; deleteItemName = '{{ addslashes($petugas->name) }} ({{ $petugas->username }})'"
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada akun petugas yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($petugasList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $petugasList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Edit Petugas -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display:none;" x-transition>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="editModal = false">
            <h3 class="text-base font-bold text-slate-800 mb-1">Edit Akun Petugas</h3>
            <p class="text-xs text-slate-500 mb-4">Ubah nama lengkap, username, atau status aktivasi petugas.</p>

            <form :action="editUrl" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editData.name" required
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" x-model="editData.username" required
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Akun</label>
                    <select name="status_akun" x-model="editData.status_akun" required
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white font-semibold">
                        <option value="aktif">Aktif (Diberikan Akses)</option>
                        <option value="menunggu_persetujuan">Menunggu Persetujuan</option>
                        <option value="nonaktif">Dinonaktifkan</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-semibold transition shadow-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password Petugas -->
    <div x-show="resetModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display:none;" x-transition>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200" @click.away="resetModal = false">
            <h3 class="text-base font-bold text-slate-800 mb-1">Atur Ulang Kata Sandi</h3>
            <p class="text-xs text-slate-500 mb-4">Mereset kata sandi untuk: <strong class="text-blue-700" x-text="resetUser"></strong></p>

            <form :action="resetUrl" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi kata sandi baru"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600">
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="resetModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold transition shadow-xs">
                        Simpan Kata Sandi Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Petugas -->
    <div x-show="deleteModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display:none;" x-transition>
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-200 text-center" @click.away="deleteModal = false">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Hapus Akun Petugas?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4">
                Apakah Anda yakin ingin menghapus akun <strong class="text-slate-800" x-text="deleteItemName"></strong>? Riwayat audit trail pelaporan akan tetap tersimpan.
            </p>

            <form :action="deleteUrl" method="POST" class="flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold transition shadow-xs">
                    Ya, Hapus Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
