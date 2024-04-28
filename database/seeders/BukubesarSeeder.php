<?php

namespace Database\Seeders;

use App\Models\BukubesarModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BukubesarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bukuBesar =  [
            [
                "id_bukubesar" => "1",
                "hash_id_bukubesar" => "ec9a4681-18a6-41a6-a590-5d41d95ba363",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-27",
                "kategori" => "modal awal",
                "keterangan" => "cccccccccccc",
                "debit" => "0",
                "kredit" => "2500000",
                "created_at" => "2024-04-23 00:45:20",
                "updated_at" => "2024-04-26 10:03:55",
                "deleted_at" => NULL,
            ],
            [
                "id_bukubesar" => "2",
                "hash_id_bukubesar" => "16bfde33-def5-4c85-866e-59f9e03e925e",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-24",
                "kategori" => "stok",
                "keterangan" => "khdvuaiv",
                "debit" => "5000000",
                "kredit" => "0",
                "created_at" => "2024-04-23 01:27:07",
                "updated_at" => "2024-04-25 00:48:19",
                "deleted_at" => "2024-04-25 00:48:19",
            ],
            [
                "id_bukubesar" => "3",
                "hash_id_bukubesar" => "667e60ce-3051-4766-9b5a-66c912b6b138",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-25",
                "kategori" => "transaksi",
                "keterangan" => "untuk transaksi penjualan",
                "debit" => "3000000",
                "kredit" => "0",
                "created_at" => "2024-04-25 00:30:29",
                "updated_at" => "2024-04-25 05:58:05",
                "deleted_at" => "2024-04-25 05:58:05",
            ],
            [
                "id_bukubesar" => "4",
                "hash_id_bukubesar" => "76f6889a-249a-47aa-9a95-3ffcdc8603cd",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-25",
                "kategori" => "transaksi",
                "keterangan" => "untuk transaksi penjualan",
                "debit" => "29.892",
                "kredit" => "0",
                "created_at" => "2024-04-25 05:58:24",
                "updated_at" => "2024-04-25 06:01:13",
                "deleted_at" => "2024-04-25 06:01:13",
            ],
            [
                "id_bukubesar" => "5",
                "hash_id_bukubesar" => "74f38de0-84cb-427a-a66f-c3158dda34d0",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-25",
                "kategori" => "transaksi",
                "keterangan" => "untuk transaksi penjualan",
                "debit" => "29892000",
                "kredit" => "0",
                "created_at" => "2024-04-25 06:00:04",
                "updated_at" => "2024-04-25 06:00:04",
                "deleted_at" => NULL,
            ],
            [
                "id_bukubesar" => "6",
                "hash_id_bukubesar" => "f893adf4-6243-4400-9068-49a2aa926cf6",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-21",
                "kategori" => "transaksi",
                "keterangan" => "khdvuaiv",
                "debit" => "30000000",
                "kredit" => "0",
                "created_at" => "2024-04-25 06:04:10",
                "updated_at" => "2024-04-25 06:04:10",
                "deleted_at" => NULL,
            ],
            [
                "id_bukubesar" => "7",
                "hash_id_bukubesar" => "9cad9d50-17d3-4c18-98d4-4da969a07e08",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-25",
                "kategori" => "modal awal",
                "keterangan" => "dana darurat",
                "debit" => "0",
                "kredit" => "463000",
                "created_at" => "2024-04-25 23:53:02",
                "updated_at" => "2024-04-25 23:53:02",
                "deleted_at" => NULL,
            ],
            [
                "id_bukubesar" => "8",
                "hash_id_bukubesar" => "76f38c93-13ed-42e3-b72c-c49a4a5090b1",
                "id_akunbayar" => "1",
                "tanggal" => "2024-04-25",
                "kategori" => "modal awal",
                "keterangan" => "modal awal yang tersedia",
                "debit" => "361000",
                "kredit" => "0",
                "created_at" => "2024-04-25 23:55:45",
                "updated_at" => "2024-04-25 23:55:45",
                "deleted_at" => NULL,
            ],
        ];
        


        foreach ($bukuBesar as $dataBkBsr) {
            BukubesarModel::create($dataBkBsr);
        }
    }
}
