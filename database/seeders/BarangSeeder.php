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

        $SemuaBarang = [
            [
                'nama_barang' => 'Galvalum Coil 0.5mm',
                'harga_barang' => 200000,
                'stok' => 80,
                'ukuran' => '0.5mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 7, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Pipe 1 inch',
                'harga_barang' => 180000,
                'stok' => 50,
                'ukuran' => '1 inch',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 8, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Wire 2.5mm',
                'harga_barang' => 220000,
                'stok' => 60,
                'ukuran' => '2.5mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 9, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Angle 40x40x3mm',
                'harga_barang' => 190000,
                'stok' => 70,
                'ukuran' => '40x40x3mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 10, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Channel 50x25x2mm',
                'harga_barang' => 210000,
                'stok' => 55,
                'ukuran' => '50x25x2mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 11, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Rod 6mm',
                'harga_barang' => 230000,
                'stok' => 45,
                'ukuran' => '6mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 12, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Beam 100x50x5mm',
                'harga_barang' => 250000,
                'stok' => 65,
                'ukuran' => '100x50x5mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 13, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Plate 2mm',
                'harga_barang' => 280000,
                'stok' => 75,
                'ukuran' => '2mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 14, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Mesh 50x50x3mm',
                'harga_barang' => 270000,
                'stok' => 85,
                'ukuran' => '50x50x3mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 15, // ID Tipe Barang yang sudah ada dalam database
            ],
         
        ];
        foreach ($SemuaBarang as $dtbarang) {
            Barang::create($dtbarang);

        }
    }
}
