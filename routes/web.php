<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangBuktiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemilikPengemudiController;
use App\Http\Controllers\PenertibanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\TrayekController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', fn () => redirect()->route('login'));

// Auth routes (hanya untuk guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==============================================================
// RUANG KERJA KHUSUS PETUGAS PELAPOR (HAK AKSES TERBATAS)
// ==============================================================
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/input', [PetugasController::class, 'inputForm'])->name('input');
    Route::post('/input', [PetugasController::class, 'storeKendaraan'])->name('input.store');
    Route::get('/riwayat', [PetugasController::class, 'riwayat'])->name('riwayat');
    Route::get('/profil', [PetugasController::class, 'profil'])->name('profil');
    Route::put('/profil/password', [PetugasController::class, 'updatePassword'])->name('profil.password');
});

// ==============================================================
// DATABASE KENDARAAN (SUPERADMIN, ADMIN, OPERATOR, PETUGAS)
// ==============================================================
Route::middleware(['auth', 'role:superadmin,admin,operator,petugas'])->group(function () {
    Route::resource('kendaraan', KendaraanController::class)->withTrashed(['show']);

    Route::prefix('kendaraan/{kendaraan}/pemilik-pengemudi')->name('pemilik-pengemudi.')->group(function () {
        Route::get('/create', [PemilikPengemudiController::class, 'create'])->name('create');
        Route::post('/', [PemilikPengemudiController::class, 'store'])->name('store');
        Route::get('/{pemilikPengemudi}/edit', [PemilikPengemudiController::class, 'edit'])->name('edit');
        Route::put('/{pemilikPengemudi}', [PemilikPengemudiController::class, 'update'])->name('update');
        Route::delete('/{pemilikPengemudi}', [PemilikPengemudiController::class, 'destroy'])->name('destroy');
    });
});

// ==============================================================
// AREA ADMINISTRATIF (SUPERADMIN, ADMIN, OPERATOR)
// Petugas dilarang mengakses area ini (403 Forbidden via middleware)
// ==============================================================
Route::middleware(['auth', 'role:superadmin,admin,operator'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Penertiban (dengan parameter konsisten penertib)
    Route::get('/penertiban/{penertib}/cetak', [PenertibanController::class, 'cetak'])->name('penertiban.cetak');
    Route::resource('penertiban', PenertibanController::class)->parameters(['penertiban' => 'penertib']);

    // Barang Bukti (dengan parameter konsisten barangBukti)
    Route::resource('barang-bukti', BarangBuktiController::class)->parameters(['barang-bukti' => 'barangBukti']);

    // Trayek
    Route::resource('trayek', TrayekController::class)->except(['show']);

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.csv');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
    Route::put('/pengaturan/profil', [PengaturanController::class, 'updateProfile'])->name('pengaturan.profil');
    Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');
});

// ==============================================================
// KHUSUS SUPERADMIN
// ==============================================================
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // Pengelolaan Petugas Lapangan
    Route::prefix('admin/petugas')->name('admin.petugas.')->group(function () {
        Route::get('/', [AdminController::class, 'petugasIndex'])->name('index');
        Route::put('/{user}/toggle-status', [AdminController::class, 'petugasToggleStatus'])->name('toggle');
        Route::put('/{user}/update', [AdminController::class, 'petugasUpdate'])->name('update');
        Route::put('/{user}/reset-password', [AdminController::class, 'petugasResetPassword'])->name('reset-password');
        Route::delete('/{user}', [AdminController::class, 'petugasDestroy'])->name('destroy');
    });

    // Manajemen Admin
    Route::resource('admin', AdminController::class)->except(['show']);
});
