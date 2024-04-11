<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Barang::create([
            'nama_barang' => 'Nama Barang',
            'harga_barang' => 10000,
            'stok' => 10,
            'ukuran' => 'M',
            'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
            'id_tipe_barang' => 1, // ID Tipe Barang yang sudah ada dalam database
        ]);
    }
}
