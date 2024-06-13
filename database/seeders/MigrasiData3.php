<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\StokBarangModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrasiData3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // //  Untuk mencari yang stok barang yang tambah barang
        // $seluruhBukubesar = BukubesarModel::all();

        // // Loop untuk melakukan sesuatu dengan setiap entri
        // foreach ($seluruhBukubesar as $bukubesar) {
        //     // Definisikan pola yang sesuai dengan format yang diinginkan
        //     $polaSTokbarangTambah = '/^STOK BARANG \d+ STOK- \d+$/';

        //     // Lakukan pencocokan dengan pola menggunakan preg_match
        //     $keterangan = $bukubesar->keterangan;
        //     if (preg_match($polaSTokbarangTambah, $keterangan)) {



        //         // Pisahkan string menjadi array menggunakan spasi sebagai pemisah
        //         $arrayBarang = explode(' ', $keterangan);

        //         // Input untuk stok barang baru
        //         $stokbarang = new StokBarangModel();
        //         $stokbarang->id_barang = $arrayBarang[2];
        //         $stokbarang->stok_masuk = $arrayBarang[4];
        //         $stokbarang->save();

        //     } else {
        //         // echo "String tidak sesuai dengan format yang diinginkan: $keterangan";
        //     }
        // }


        // Stok Barang total akhir
        // Pesanan Pembeli
        $jsonStok_barang = file_get_contents(public_path('barang_stok.json'));
        $Stok_barang = json_decode($jsonStok_barang, true);
        foreach ($Stok_barang as $id_barang => $data) {
            

            // $stokBarang->
            $totalStok = DB::table('stok_barang')
                ->selectRaw('SUM(stok_masuk) - SUM(stok_keluar) as stok')
                ->where('id_barang', $id_barang)
                ->value('stok');
          
            $barang = Barang::find($id_barang);
            $barang->stok = $data['stok'];
            $barang->save();
        }
    }
}
