<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\DaftarTransaksiController;
use App\Http\Controllers\JsonType\BarangJsonController;
use App\Http\Controllers\JsonType\PembeliJsonController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemasokBarangController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    // Master User
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/', [UserController::class, 'store'])->name('user.store'); // Menyimpan user baru
        Route::put('/{id}', [UserController::class, 'update'])->name('user.update'); // Mengupdate user berdasarkan ID
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy'); // Menghapus user berdasarkan ID
    });

    // Master Tipe Barang
    Route::prefix('tipe-barang')->group(function () {
        Route::get('/', [TipeBarangController::class, 'index'])->name('tipebarang.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [TipeBarangController::class, 'edit'])->name('tipebarang.edit');
        Route::post('/', [TipeBarangController::class, 'store'])->name('tipebarang.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [TipeBarangController::class, 'update'])->name('tipebarang.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [TipeBarangController::class, 'destroy'])->name('tipebarang.destroy'); // Menghapus tipebarang berdasarkan ID
    });
    // Master Pemasok Barang
    Route::prefix('pemasok-barang')->group(function () {
        Route::get('/', [PemasokBarangController::class, 'index'])->name('pemasokbarang.index');
        Route::get('/edit/{id}', [PemasokBarangController::class, 'edit'])->name('pemasokbarang.edit');
        Route::post('/', [PemasokBarangController::class, 'store'])->name('pemasokbarang.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [PemasokBarangController::class, 'update'])->name('pemasokbarang.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [PemasokBarangController::class, 'destroy'])->name('pemasokbarang.destroy'); // Menghapus tipebarang berdasarkan ID
    });
    // Master Pemasok Barang
    Route::prefix('pembeli')->group(function () {
        Route::get('/', [PembeliController::class, 'index'])->name('pembeli.index');
        Route::get('/edit/{id}', [PembeliController::class, 'edit'])->name('pembeli.edit');
        Route::post('/', [PembeliController::class, 'store'])->name('pembeli.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [PembeliController::class, 'update'])->name('pembeli.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [PembeliController::class, 'destroy'])->name('pembeli.destroy'); // Menghapus tipebarang berdasarkan ID
    });

    // Route untuk json type api internal
    Route::prefix('jsontype')->group(function () {
        Route::get('/semuabarang', [BarangJsonController::class, 'getSemuaBarangData'])->name('json.semuabarang');
        Route::get('/semuapembeli', [PembeliJsonController::class, 'getSemuaPembeliData'])->name('json.semuapembeli');
    });


    Route::prefix('pemesanan')->group(function () {
        Route::post('/', [PembelianController::class, 'store'])->name('pemesanan.store');
        // Route::get('/', [PembelianController::class, 'index']);
        Route::get('/edit/{id}', [PembelianController::class, 'edit'])->name('pemesanan.edit');
        Route::put('/{id}', [PembelianController::class, 'update'])->name('pemesanan.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [PembelianController::class, 'destroy'])->name('pemesanan.destroy'); // Menghapus tipebarang berdasarkan ID
    });

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::prefix('stok')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('stok.index');
        Route::get('/edit/{id}', [StokController::class, 'edit'])->name('stok.edit');
        Route::post('/', [StokController::class, 'store'])->name('stok.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [StokController::class, 'update'])->name('stok.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [StokController::class, 'destroy'])->name('stok.destroy'); // Menghapus tipebarang berdasarkan ID
    });

    Route::prefix('daftar_transaksi')->group(function () {
        Route::get('/', [DaftarTransaksiController::class, 'index'])->name('pemesanan.index');
        Route::get('/info/{id}', [DaftarTransaksiController::class, 'daftarBarangPesanan'])->name('pemesanan.infobarang');
    });
    Route::prefix('retur')->group(function () {
        Route::get('/', [ReturController::class, 'index'])->name('retur.index');
        Route::get('/add/{id_pesanan}', [ReturController::class, 'add'])->name('retur.add');
        Route::post('/', [ReturController::class, 'store'])->name('retur.store');
        // Route::get('/', [ReturController::class, 'index']);
        Route::get('/edit/{id_retur}', [ReturController::class, 'edit'])->name('retur.edit');
        Route::put('/{id_retur}', [ReturController::class, 'update'])->name('retur.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id_retur}', [ReturController::class, 'destroy'])->name('retur.destroy'); // Menghapus tipebarang berdasarkan ID
    });
});
