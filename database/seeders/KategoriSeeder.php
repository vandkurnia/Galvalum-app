<?php

namespace Database\Seeders;

use App\Models\KategoriModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tambahkan data ke tabel kategori
        KategoriModel::create(['nama_kategori' => 'transaksi']);
        KategoriModel::create(['nama_kategori' => 'kas_keluar']);
        KategoriModel::create(['nama_kategori' => 'modal_tambahan']);
        KategoriModel::create(['nama_kategori' => 'barang']);
    }
}
