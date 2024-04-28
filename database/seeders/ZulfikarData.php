<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZulfikarData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $modal_tambahan = [
            [
                "id_modal_tambahan" => "1",
                "jenis_modal_tambahan" => "Modal Awal",
                "deskripsi" => "Modal awal untuk membuat usaha",
                "jumlah_modal" => "4000000",
                "tanggal" => "2024-04-24",
                "created_at" => "2024-04-24 05:26:36",
                "updated_at" => "2024-04-24 05:37:45",
                "deleted_at" => "2024-04-24 05:37:45",
            ],
            [
                "id_modal_tambahan" => "2",
                "jenis_modal_tambahan" => "Modal Awal",
                "deskripsi" => "Modal awal untuk usaha",
                "jumlah_modal" => "4500000",
                "tanggal" => "2024-04-27",
                "created_at" => "2024-04-24 05:29:00",
                "updated_at" => "2024-04-26 10:06:37",
                "deleted_at" => NULL,
            ],
            [
                "id_modal_tambahan" => "3",
                "jenis_modal_tambahan" => "Dp Pak Baret",
                "deskripsi" => "Modal penjualan sparepart",
                "jumlah_modal" => "3000000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-24 06:12:25",
                "updated_at" => "2024-04-25 06:50:41",
                "deleted_at" => NULL,
            ],
            [
                "id_modal_tambahan" => "4",
                "jenis_modal_tambahan" => "Kembalian lebih Pak Heru",
                "deskripsi" => "donasi dari kembalian",
                "jumlah_modal" => "1000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 06:51:34",
                "updated_at" => "2024-04-25 06:51:34",
                "deleted_at" => NULL,
            ],
            [
                "id_modal_tambahan" => "5",
                "jenis_modal_tambahan" => "Dp Pak Yanto Pasar Pvc",
                "deskripsi" => "Konsumsi dalam rangka rapat",
                "jumlah_modal" => "3000000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 06:52:43",
                "updated_at" => "2024-04-25 06:52:43",
                "deleted_at" => NULL,
            ],
        ];
        DB::table('modal_tambahan')->insert($modal_tambahan);
        $kategori = [
            [
                "id_kategori" => "1",
                "nama_kategori" => "transaksi",
                "created_at" => NULL,
                "updated_at" => NULL,
                "deleted_at" => NULL,
            ],
            [
                "id_kategori" => "2",
                "nama_kategori" => "modal awal",
                "created_at" => NULL,
                "updated_at" => NULL,
                "deleted_at" => NULL,
            ],
            [
                "id_kategori" => "3",
                "nama_kategori" => "stok barang",
                "created_at" => NULL,
                "updated_at" => "2024-04-27 01:34:14",
                "deleted_at" => NULL,
            ],
            [
                "id_kategori" => "4",
                "nama_kategori" => "hutang",
                "created_at" => "2024-04-27 01:00:12",
                "updated_at" => "2024-04-27 01:08:15",
                "deleted_at" => "2024-04-27 01:08:15",
            ],
        ];


        DB::table('kategori')->insert($kategori);

        $kas_keluar = [
            [
                "id_kas_keluar" => "1",
                "nama_pengeluaran" => "Konsumsi",
                "deskripsi" => "Konsumsi dalam rangka rapat",
                "jumlah_pengeluaran" => "30000",
                "tanggal" => "2024-04-23",
                "created_at" => "2024-04-23 07:38:40",
                "updated_at" => "2024-04-23 07:59:59",
                "deleted_at" => "2024-04-23 07:59:59",
            ],
            [
                "id_kas_keluar" => "2",
                "nama_pengeluaran" => "Konsumsi pasar",
                "deskripsi" => "Konsumsi dalam rangka rapat",
                "jumlah_pengeluaran" => "30000",
                "tanggal" => "2024-04-23",
                "created_at" => "2024-04-23 08:00:16",
                "updated_at" => "2024-04-26 10:47:15",
                "deleted_at" => "2024-04-26 10:47:15",
            ],
            [
                "id_kas_keluar" => "3",
                "nama_pengeluaran" => "Pembelian perabotan",
                "deskripsi" => "membeli bahan perabotan untuk renofasi kantor",
                "jumlah_pengeluaran" => "4500000",
                "tanggal" => "2024-04-24",
                "created_at" => "2024-04-23 22:30:51",
                "updated_at" => "2024-04-25 08:08:18",
                "deleted_at" => "2024-04-25 08:08:18",
            ],
            [
                "id_kas_keluar" => "4",
                "nama_pengeluaran" => "Transfer Pak Daman",
                "deskripsi" => "ditransfer ke pak darman",
                "jumlah_pengeluaran" => "1230000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 07:54:23",
                "updated_at" => "2024-04-27 07:20:44",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "5",
                "nama_pengeluaran" => "Transfer Pak Mariono",
                "deskripsi" => "di transfer ke pak mariono",
                "jumlah_pengeluaran" => "90000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 07:57:40",
                "updated_at" => "2024-04-25 07:57:40",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "6",
                "nama_pengeluaran" => "Bayar Skrup Pak Rembo",
                "deskripsi" => "untuk pembayaran skrup di toko pak rembo",
                "jumlah_pengeluaran" => "720000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 07:58:49",
                "updated_at" => "2024-04-25 07:58:49",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "7",
                "nama_pengeluaran" => "Sangu Kuli Star",
                "deskripsi" => "gaji kuli harian",
                "jumlah_pengeluaran" => "30000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 07:59:38",
                "updated_at" => "2024-04-25 07:59:38",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "8",
                "nama_pengeluaran" => "Bayar Ornamen Pak Abdul",
                "deskripsi" => "pembayaran ornamen di toko pak abdul",
                "jumlah_pengeluaran" => "870000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 08:01:17",
                "updated_at" => "2024-04-25 08:01:17",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "9",
                "nama_pengeluaran" => "Sangu bongkar holo GT",
                "deskripsi" => "biaya bongkaran holo",
                "jumlah_pengeluaran" => "10000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 08:02:07",
                "updated_at" => "2024-04-25 08:02:07",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "10",
                "nama_pengeluaran" => "Transfer Pak Deni Bulak",
                "deskripsi" => "ditransfer ke rekening pak deni",
                "jumlah_pengeluaran" => "1140000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 08:03:28",
                "updated_at" => "2024-04-25 08:03:28",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "11",
                "nama_pengeluaran" => "Diskon Pak Irfan",
                "deskripsi" => "potongan harga dari pak irfan",
                "jumlah_pengeluaran" => "1000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 08:04:26",
                "updated_at" => "2024-04-25 08:04:26",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "12",
                "nama_pengeluaran" => "Setor Modal Sore 30/11/2023 Jam 14.41",
                "deskripsi" => "setoran sesuai tanggal di judul",
                "jumlah_pengeluaran" => "7700000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 08:07:45",
                "updated_at" => "2024-04-25 08:07:45",
                "deleted_at" => NULL,
            ],
            [
                "id_kas_keluar" => "13",
                "nama_pengeluaran" => "Setor Modal Sore 30/11/2023 Jam 15.34",
                "deskripsi" => "membeli bahan perabotan untuk renofasi kantor",
                "jumlah_pengeluaran" => "1600000",
                "tanggal" => "2024-04-25",
                "created_at" => "2024-04-25 08:09:26",
                "updated_at" => "2024-04-25 08:09:26",
                "deleted_at" => NULL,
            ],
        ];
        DB::table('kas_keluar')->insert($kas_keluar);
    }
}
