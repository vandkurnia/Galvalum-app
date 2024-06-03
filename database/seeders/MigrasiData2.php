<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BukubesarBarangModel;
use App\Models\BukubesarModel;
use App\Models\DiskonModel;
use App\Models\Notabukubesar;
use App\Models\NotaPembeli;
use App\Models\pdf\SuratJalanModel;
use App\Models\PemasokBarang;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use App\Models\Retur\ReturPembeliModel;
use App\Models\Retur\ReturPesananPembeliModel;
use App\Models\StokBarangModel;
use App\Models\TipeBarang;
use App\Models\User;
use Carbon\Carbon;
use Faker\Core\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrasiData2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersjson = file_get_contents(public_path('datamigration/users.json'));
        $users = json_decode($usersjson, true);

        foreach ($users[2]['data'] as $item) {
            User::create([
                'id_admin' => $item['id_admin'],
                'hash_id_admin' => $item['hash_id_admin'],
                'nama_admin' => $item['nama_admin'],
                'no_telp_admin' => $item['no_telp_admin'],
                'email_admin' => $item['email_admin'],
                'password' => $item['password'],
                'role' => $item['role'],
                'remember_token' => $item['remember_token'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }

        // Diskon
        $jsondiskon = file_get_contents(public_path('datamigration/diskon.json'));
        $diskon = json_decode($jsondiskon, true);

        foreach ($diskon[2]['data'] as $item) {
            DiskonModel::create([
                'id_diskon' => $item['id_diskon'],
                'hash_id_diskon' => $item['hash_id_diskon'],
                'kode_diskon' => $item['kode_diskon'],
                'nama_diskon' => $item['nama_diskon'],
                'type' => $item['type'],
                'besaran' => $item['besaran'],
                'status' => $item['status'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }

        // Pembeli Barangs
        $jsonpembeli = file_get_contents(public_path('datamigration/pembelis.json'));
        $pembeli = json_decode($jsonpembeli, true);

        foreach ($pembeli[2]['data'] as $item) {
            Pembeli::create([
                'id_pembeli' => $item['id_pembeli'],
                'hash_id_pembeli' => $item['hash_id_pembeli'],
                'nama_pembeli' => $item['nama_pembeli'],
                'alamat_pembeli' => $item['alamat_pembeli'],
                'jenis_pembeli' => $item['jenis_pembeli'],
                'no_hp_pembeli' => $item['no_hp_pembeli'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }


        // Pemasok barangs
        $jsonpemasok_barangs = file_get_contents(public_path('datamigration/pemasok_barangs.json'));
        $pemasok_barangs = json_decode($jsonpemasok_barangs, true);

        foreach ($pemasok_barangs[2]['data'] as $item) {
            PemasokBarang::create([
                'id_pemasok' => $item['id_pemasok'],
                'hash_id_pemasok' => $item['hash_id_pemasok'],
                'nama_pemasok' => $item['nama_pemasok'],
                'no_telp_pemasok' => $item['no_telp_pemasok'],
                'alamat_pemasok' => $item['alamat_pemasok'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }


        // Tipe Barang
        $tipe_barangjson = file_get_contents(public_path('datamigration/tipe_barangs.json'));
        $tipe_barang = json_decode($tipe_barangjson, true);

        foreach ($tipe_barang[2]['data'] as $item) {
            TipeBarang::create([
                'id_tipe_barang' => $item['id_tipe_barang'],
                'hash_id_tipe_barang' => $item['hash_id_tipe_barang'],
                'nama_tipe' => $item['nama_tipe'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }
        // Barangs
        $jsonbarangs = file_get_contents(public_path('datamigration/barangs.json'));

        $databarangs = json_decode($jsonbarangs, true);

        foreach ($databarangs[2]['data'] as $item) {
            $barangs =  Barang::create([
                'id_barang' => $item['id_barang'],
                'hash_id_barang' => $item['hash_id_barang'],
                'kode_barang' => $item['kode_barang'],
                'nama_barang' => $item['nama_barang'],
                'harga_barang' => $item['harga_barang'],
                'harga_barang_pemasok' => $item['harga_barang_pemasok'],

                'ukuran' => $item['ukuran'],
                // 'status_pembayaran' => $item['status_pembayaran'],
                'total' => $item['total'],
                'nominal_terbayar' => $item['nominal_terbayar'],
                'tenggat_bayar' => $item['tenggat_bayar'],
                'id_pemasok' => $item['id_pemasok'],
                'id_tipe_barang' => $item['id_tipe_barang'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
            StokBarangModel::create([
                'id_barang' => $barangs->id_barang,  // Ensure this matches an existing id_barang in barangs table
                'stok_masuk' => 0,
                'tipe_stok' => 'stokbarang'
            ]);
        }








        // Nota Pembeli Section

        // Nota Pembeli
        $jsonNotaPembeli = file_get_contents(public_path('datamigration/nota_pembelis.json'));
        $notaPembeli = json_decode($jsonNotaPembeli, true);

        foreach ($notaPembeli[2]['data'] as $item) {
            NotaPembeli::create([
                'id_nota' => $item['id_nota'],
                'no_nota' => $item['no_nota'],
                'id_pembeli' => $item['id_pembeli'],
                'id_admin' => $item['id_admin'],
                'metode_pembayaran' => $item['metode_pembayaran'],
                // 'status_pembayaran' => $item['status_pembayaran'],
                'sub_total' => $item['sub_total'],
                'nominal_terbayar' => $item['nominal_terbayar'],
                'tenggat_bayar' => $item['tenggat_bayar'],
                'diskon' => $item['diskon'],
                'ongkir' => $item['ongkir'],
                'total' => $item['total'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }


        // // Stok Barang
        // // Path to the JSON file
        // $jsonFilePath = public_path('datamigration/stok_barang.json');

        // // Get the JSON data
        // $jsonStokBarang = file_get_contents($jsonFilePath);
        // $stokBarangData = json_decode($jsonStokBarang, true);

        // // Iterate through each item and create a new StokBarang record

        // foreach ($stokBarangData[2]['data'] as $item) {
        //     // $id = $item['id'];
        //     // $stokMasuk = $item['stok_masuk'];
        //     // $stokKeluar = $item['stok_keluar'];

        //     // $netStok = $stokMasuk - $stokKeluar;

        //     // if (isset($stokBarangList[$id])) {
        //     //     // If the ID already exists, update the stock
        //     //     $stokBarangList[$id] += $netStok;
        //     // } else {
        //     //     // If the ID does not exist, set the initial stock
        //     //     $stokBarangList[$id] = $netStok;
        //     // }


        //     StokBarangModel::create([
        //         'id' => $item['id'],
        //         'id_barang' => $item['id_barang'],
        //         'stok_masuk' => $item['stok_masuk'],
        //         'stok_keluar' => $item['stok_keluar'],
        //         'created_at' => Carbon::parse($item['created_at']),
        //         'updated_at' => Carbon::parse($item['updated_at']),
        //         'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
        //     ]);
        // }




        // Pesanan Pembeli
        $jsonPesananPembeli = file_get_contents(public_path('datamigration/pesanan_pembelis.json'));
        $pesananPembeli = json_decode($jsonPesananPembeli, true);

        foreach ($pesananPembeli[2]['data'] as $item) {



            // $stokLama = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $item['id_barang'])->groupBy('id_barang')->first();

            $stokBarang = StokBarangModel::create([
                'stok_keluar' => $item['jumlah_pembelian'],
                'id_barang' => $item['id_barang'],
                'tipe_stok' => 'pesanan'
            ]);


            // Buat entri baru di PesananPembeli
            PesananPembeli::create([
                'id_pesanan' => $item['id_pesanan'],
                'jumlah_pembelian' => $item['jumlah_pembelian'],
                'harga' => $item['harga'],
                'diskon' => $item['diskon'],
                'id_nota' => $item['id_nota'],
                'id_barang' => $item['id_barang'],
                'jenis_pembelian' => $item['jenis_pembelian'],
                'harga_potongan' => $item['harga_potongan'],
                'id_diskon' => $item['id_diskon'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
                'id_stokbarang' => $stokBarang->id
            ]);

          
        }


        // Surat Jalan
        $jsonsuratJalan = file_get_contents(public_path('datamigration/surat_jalan.json'));
        $suratJalan = json_decode($jsonsuratJalan, true);

        foreach ($suratJalan[2]['data'] as $item) {
            DB::table('surat_jalan')->insert([
                'id_surat_jalan' => $item['id_surat_jalan'],
                'no_surat_jalan' => $item['no_surat_jalan'],
                'id_nota' => $item['id_nota'],
                'users' => 1,
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }

        // BukuBesar
        $bukubesarjson = file_get_contents(public_path('datamigration/bukubesar.json'));
        $bukubesar = json_decode($bukubesarjson, true);

        foreach ($bukubesar[2]['data'] as $item) {
            BukubesarModel::create([
                'id_bukubesar' => $item['id_bukubesar'],
                'hash_id_bukubesar' => $item['hash_id_bukubesar'],
                'id_akunbayar' => $item['id_akunbayar'],
                'tanggal' => $item['tanggal'],
                'kategori' => $item['kategori'],
                'keterangan' => $item['keterangan'],
                'debit' => $item['debit'],
                'kredit' => $item['kredit'],
                // 'sub_kategori' => $item['sub_kategori'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }

        // Buku Besar Barang
        $jsonbukubesarBarang = file_get_contents(public_path('datamigration/bukubesar_barang.json'));
        $bukubesarBarang = json_decode($jsonbukubesarBarang, true);

        foreach ($bukubesarBarang[2]['data'] as $item) {
            BukubesarBarangModel::create([
                'id' => $item['id'],
                'id_bukubesar' => $item['id_bukubesar'],
                'id_barang' => $item['id_barang'],
                'created_at' => $item['created_at'] ? Carbon::parse($item['created_at']) : null,
                'updated_at' => $item['updated_at'] ? Carbon::parse($item['updated_at']) : null,
            ]);
        }
        // Nota BukuBesar
        $jsonnota_bukubesar = file_get_contents(public_path('datamigration/nota_bukubesar.json'));
        $notaBukubesar = json_decode($jsonnota_bukubesar, true);

        foreach ($notaBukubesar[2]['data'] as $item) {
            Notabukubesar::create([
                'id_notabukubesar' => $item['id_notabukubesar'],
                'id_nota' => $item['id_nota'],
                'id_bukubesar' => $item['id_bukubesar'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
            ]);
        }




        // 
        // Retur Section //
        //  Retur Pembeli
        $jsonFilePath = public_path('datamigration/retur_pembeli.json');

        // Get the JSON data
        $jsonReturPembeli = file_get_contents($jsonFilePath);
        $returPembeliData = json_decode($jsonReturPembeli, true);

        // Iterate through each item and create a new ReturPembeli record
        foreach ($returPembeliData[2]['data'] as $item) {
            ReturPembeliModel::create([
                'id_retur_pembeli' => $item['id_retur_pembeli'],
                'hash_id_retur_pembeli' => $item['hash_id_retur_pembeli'],
                'id_nota' => $item['id_nota'],
                'no_retur_pembeli' => $item['no_retur_pembeli'],
                'faktur_retur_pembeli' => $item['faktur_retur_pembeli'],
                'tanggal_retur_pembeli' => $item['tanggal_retur_pembeli'],
                'bukti_retur_pembeli' => $item['bukti_retur_pembeli'],
                'jenis_retur' => $item['jenis_retur'],
                'total_nilai_retur' => $item['total_nilai_retur'],
                'pengembalian_data' => $item['pengembalian_data'],
                'kekurangan' => $item['kekurangan'],
                'status' => $item['status'],
                'id_pembeli' => $item['id_pembeli'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }


        //  Retur Pesanan Pembeli
        // Path to the JSON file
        $jsonFilePath = public_path('datamigration/retur_pesanan_pembeli.json');

        // Get the JSON data
        $jsonReturPesananPembeli = file_get_contents($jsonFilePath);
        $returPesananPembeliData = json_decode($jsonReturPesananPembeli, true);

        // Iterate through each item and create a new ReturPesananPembeli record
        foreach ($returPesananPembeliData[2]['data'] as $item) {
            ReturPesananPembeliModel::create([
                'id_retur_pesanan' => $item['id_retur_pesanan'],
                'id_retur_pembeli' => $item['id_retur_pembeli'],
                'id_pesanan_pembeli' => $item['id_pesanan_pembeli'],
                'harga' => $item['harga'],
                'total' => $item['total'],
                'qty' => $item['qty'],
                'qty_sebelum_perubahan' => $item['qty_sebelum_perubahan'],
                'type_retur_pesanan' => $item['type_retur_pesanan'],
                'created_at' => Carbon::parse($item['created_at']),
                'updated_at' => Carbon::parse($item['updated_at']),
                'deleted_at' => $item['deleted_at'] ? Carbon::parse($item['deleted_at']) : null,
            ]);
        }
    }
}
