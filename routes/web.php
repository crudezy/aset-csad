<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AsetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\HistoriPemakaianController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RiwayatServiceController;
use App\Http\Controllers\VendorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Route untuk autentikasi (login, register, logout, dll.) tetap diaktifkan.
Auth::routes();

// 2. Route utama ('/') sekarang akan dialihkan ke dashboard.
//    Middleware 'auth' akan mencegat ini dan menampilkan halaman login jika pengguna belum masuk.
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});
    
// 3. Semua route aplikasi sekarang berada di dalam grup middleware 'auth'.
//    Ini memastikan hanya pengguna yang sudah login yang dapat mengakses halaman-halaman ini.
Route::middleware(['auth'])->group(function () {
    
    // --- GRUP ROUTE PROFILE ---
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changepassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Dashboard
    Route::get('/dashboard-general', [DashboardController::class, 'index'])->name('dashboard.index');

    // Halaman Gabungan Master Data
    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data.index');

    // Route Resource untuk Master Data (Create, Store, Update, Destroy)
    Route::resource('kategori', KategoriController::class);
    Route::resource('vendor', VendorController::class);
    Route::resource('lokasi', LokasiController::class)->except(['index', 'show']);
    Route::resource('departemen', DepartemenController::class)->except(['index', 'show']);
    
    // Route Resource untuk Manajemen Aset
    Route::resource('pegawai', PegawaiController::class);
    Route::delete('aset/hapus-semua', [AsetController::class, 'destroyAll'])->name('aset.destroyAll');
    Route::resource('aset', AsetController::class);
    Route::get('/aset/export/excel', [AsetController::class, 'exportExcel'])->name('aset.export.excel');
    Route::post('/asets/cetak-label/multiple', [AsetController::class, 'cetakLabelMultiple'])->name('aset.cetak.label.multiple');

    // Route Resource untuk Transaksi
    Route::get('/riwayat-service/search-aset', [RiwayatServiceController::class, 'searchAset'])->name('riwayat-service.searchAset');
    Route::resource('riwayat-service', RiwayatServiceController::class)->except(['destroy']);
    
    Route::get('/pemakaian', [HistoriPemakaianController::class, 'index'])->name('pemakaian.index');
    Route::post('/pemakaian', [HistoriPemakaianController::class, 'store'])->name('pemakaian.store');
    Route::post('/pemakaian/{aset:kode_tag}/kembalikan', [HistoriPemakaianController::class, 'kembalikan'])->name('pemakaian.kembalikan');
    Route::post('/pemakaian/kembalikan-multiple', [HistoriPemakaianController::class, 'kembalikanMultiple'])->name('pemakaian.kembalikan.multiple');
    Route::post('/pemakaian/serah-terima-cetak', [HistoriPemakaianController::class, 'cetakManual'])->name('pemakaian.serah_terima.cetak');
    Route::get('/public/aset/{kode_tag}', [AsetController::class, 'showPublic'])->name('aset.showPublic');
    
}); // Penutup middleware group
