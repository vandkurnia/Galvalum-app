<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\DaftarTransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemasokBarangController;
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

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/retur', [ReturController::class, 'index']);
    Route::get('/stok', [StokController::class, 'index']);
    Route::get('/daftar_transaksi', [DaftarTransaksiController::class, 'index']);
});
