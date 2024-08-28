<?php

namespace Database\Seeders;

use App\Http\Controllers\HutangDanStokController;
use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\StokBarangHistoryModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;

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
                'kode_barang' => 'BARANG0001',
                'harga_barang' => 200000,
                'harga_barang_pemasok' => 180000,
                'stok' => 80,
                'ukuran' => '0.5mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 7, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Pipe 1 inch',
                'kode_barang' => 'BARANG0002',
                'harga_barang' => 180000,
                'harga_barang_pemasok' => 155000,
                'stok' => 50,
                'ukuran' => '1 inch',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 8, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Wire 2.5mm',
                'kode_barang' => 'BARANG0003',
                'harga_barang' => 220000,
                'harga_barang_pemasok' => 170000,
                'stok' => 60,
                'ukuran' => '2.5mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 9, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Angle 40x40x3mm',
                'kode_barang' => 'BARANG0004',
                'harga_barang' => 190000,
                'harga_barang_pemasok' => 140000,
                'stok' => 70,
                'ukuran' => '40x40x3mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 10, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Channel 50x25x2mm',
                'kode_barang' => 'BARANG0005',
                'harga_barang' => 210000,
                'harga_barang_pemasok' => 170000,
                'stok' => 55,
                'ukuran' => '50x25x2mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 11, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Rod 6mm',
                'kode_barang' => 'BARANG0006',
                'harga_barang' => 230000,
                'harga_barang_pemasok' => 170000,
                'stok' => 45,
                'ukuran' => '6mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 12, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Beam 100x50x5mm',
                'kode_barang' => 'BARANG0007',
                'harga_barang' => 250000,
                'harga_barang_pemasok' => 200000,
                'stok' => 65,
                'ukuran' => '100x50x5mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 13, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Plate 2mm',
                'kode_barang' => 'BARANG0008',
                'harga_barang' => 280000,
                'harga_barang_pemasok' => 220000,
                'stok' => 75,
                'ukuran' => '2mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 14, // ID Tipe Barang yang sudah ada dalam database
            ],
            [
                'nama_barang' => 'Galvalum Mesh 50x50x3mm',
                'kode_barang' => 'BARANG0009',
                'harga_barang' => 270000,
                'harga_barang_pemasok' => 220000,
                'stok' => 85,
                'ukuran' => '50x50x3mm',
                'id_pemasok' => 1, // ID Pemasok yang sudah ada dalam database
                'id_tipe_barang' => 15, // ID Tipe Barang yang sudah ada dalam database
            ],

        ];
        foreach ($SemuaBarang as $dtbarang) {

            // Insert into Barang table
            $id_barang = DB::table('barangs')->insertGetId([
                'hash_id_barang' => Uuid::uuid4()->toString(),
                'nama_barang' => $dtbarang['nama_barang'],
                'kode_barang' => $dtbarang['kode_barang'],
                'harga_barang' => $dtbarang['harga_barang'],
                'harga_barang_pemasok' => $dtbarang['harga_barang_pemasok'],
                'tenggat_bayar' => date('Y-m-d H:i:s'),
                'stok' => $dtbarang['stok'],
                'id_pemasok' => $dtbarang['id_pemasok'],
                'id_tipe_barang' => $dtbarang['id_tipe_barang'],
                'ukuran' => $dtbarang['ukuran'],
                'total' => $dtbarang['harga_barang_pemasok'] * $dtbarang['stok'],
            ]);

            // Insert into BukuBesar table
            $id_bukubesar = DB::table('bukubesar')->insertGetId([
                'hash_id_bukubesar' => Uuid::uuid4()->toString(),
                'id_akunbayar' => 1, // Isi dengan nilai id_akunbayar yang sesuai
                'tanggal' => date('Y-m-d'), // Isi dengan tanggal yang sesuai
                'kategori' => "barang", // Isi dengan kategori yang sesuai
                'keterangan' => "Tambah Stok Barang " . $dtbarang['nama_barang'], // Isi dengan keterangan yang sesuai
                'debit' => $dtbarang['harga_barang_pemasok'] * $dtbarang['stok'], // Isi dengan nilai debit yang sesuai
            ]);
            $barangAfterUpdate = DB::table('barangs')->where('id_barang', $id_barang)->first();

            // Hutang dan Stok Barang
            $hutangDanStokBarangController = new HutangDanStokController();
            $statusStokBarang = $hutangDanStokBarangController->store([
                'harga_beli' => $dtbarang['harga_barang_pemasok'],
                'total' => $dtbarang['harga_barang_pemasok'] * $dtbarang['stok'],
                'dp' => $dtbarang['harga_barang_pemasok'] * $dtbarang['stok'],
                'nominal_terbayar' =>  0,
                'stok' => $dtbarang['stok'],
                'tenggat_bayar' => $barangAfterUpdate->tenggat_bayar,
                'id_bukubesar' => $id_bukubesar,
                'id_barang' => $id_barang,
            ]);

            if ($statusStokBarang['status'] === 'error') {
                // Jika terjadi error, rollback dan redirect back dengan pesan error
                return $statusStokBarang['message'];
            }

            // Insert into StokBarangHistory table
            $id_stok_history = DB::table('stok_barang_history')->insertGetId([
                'id_barang' => $id_barang,
                'stok_masuk' => $dtbarang['stok'],
                'stok_terkini' => $dtbarang['stok'],
            ]);

            // Insert into LogStokBarang table
            DB::table('log_stok_barang')->insert([
                'json_content' => json_encode([
                    'type' => 'stok_store',
                    'data' => [],
                ]), // Sesuaikan dengan isi json_content Anda
                'tipe_log' => 'barang_create',
                'keterangan' => 'Tambah barang ke stok dengan total stok awal ' . $dtbarang['stok'],
                'id_admin' => 1, // Sesuaikan dengan id_admin yang ada
                'id_barang' => $id_barang, // Sesuaikan dengan id_barang yang ada
                'id_stok_barang_history' => $id_stok_history,
            ]);
        }
    }
}
