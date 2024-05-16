<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\Pembeli;
use App\Models\StokBarangModel;
use App\Models\TipeBarang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MigrasiData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $dataPembeli = [
            [
                "No" => "1",
                "Nama" => "Pak Rohmad ",
                "Alamat" => "Kinandang Magetan",
                "No Telp" => "0881026161335"
            ],
            [
                "No" => "2",
                "Nama" => "Pak Pardi",
                "Alamat" => "Jiwan Madiun",
                "No Telp" => ""
            ],
            [
                "No" => "3",
                "Nama" => "Pak Aziz/Pardi ",
                "Alamat" => "Jiwan Madiun",
                "No Telp" => "085855651946"
            ],
            [
                "No" => "4",
                "Nama" => "Pak Wahyu Karya Mandiri Bersama",
                "Alamat" => "Sogaten Madiun",
                "No Telp" => "08990356902"
            ],
            [
                "No" => "5",
                "Nama" => "Pak Afidz",
                "Alamat" => "Ds. Bibrik Kec. Jiwan Madiun",
                "No Telp" => ""
            ],
            [
                "No" => "6",
                "Nama" => "Pak Bagus Pojok ",
                "Alamat" => "Ds. Sukolilo Kec. Jiwan Madiun",
                "No Telp" => ""
            ],
            [
                "No" => "7",
                "Nama" => "Pak Budi Grobogan",
                "Alamat" => "Ds. Grobogan Kec. Jiwan Madiun",
                "No Telp" => "085704280610"
            ],
            [
                "No" => "8",
                "Nama" => "Tb. Mulia Artzen",
                "Alamat" => "Ds. Klagen Serut Kec. Jiwan Madiun",
                "No Telp" => "085748111390"
            ],
            [
                "No" => "9",
                "Nama" => "Pak Bari",
                "Alamat" => "Grobogan",
                "No Telp" => ""
            ],
            [
                "No" => "10",
                "Nama" => "Pak Mariono",
                "Alamat" => "Grobogan",
                "No Telp" => "085336747355"
            ],
            [
                "No" => "11",
                "Nama" => "Pak Heri",
                "Alamat" => "Waduk Takeran",
                "No Telp" => "082333933210"
            ],
            [
                "No" => "12",
                "Nama" => "Pak Mamat",
                "Alamat" => "Barat Magetan",
                "No Telp" => ""
            ],
            [
                "No" => "13",
                "Nama" => "Pak Dwi Darmanto (Gawok)",
                "Alamat" => "Ds. Kwangsen Jiwan",
                "No Telp" => ""
            ],
            [
                "No" => "14",
                "Nama" => "Pak Heri",
                "Alamat" => "Grobogan",
                "No Telp" => ""
            ],
            [
                "No" => "15",
                "Nama" => "Pak Fuad",
                "Alamat" => "Madiun",
                "No Telp" => ""
            ],
            [
                "No" => "16",
                "Nama" => "Pak Sutarman",
                "Alamat" => "Jiwan Madiun",
                "No Telp" => ""
            ],
            [
                "No" => "17",
                "Nama" => "Pak Endrik",
                "Alamat" => "Ponorogo",
                "No Telp" => ""
            ],
            [
                "No" => "18",
                "Nama" => "Pak Hanif/Antok Pvc",
                "Alamat" => "Magetan",
                "No Telp" => ""
            ],
            [
                "No" => "19",
                "Nama" => "Tb. Slamet Sentosa",
                "Alamat" => "Bibrik",
                "No Telp" => ""
            ],
            [
                "No" => "20",
                "Nama" => "Te'an Karya Tehknik",
                "Alamat" => "Madiun",
                "No Telp" => "081294189189"
            ],
            [
                "No" => "21",
                "Nama" => "Pak Sis ",
                "Alamat" => "Selo Magetan",
                "No Telp" => ""
            ],
            [
                "No" => "22",
                "Nama" => "Pak Sugeng",
                "Alamat" => "Klagen Panggung",
                "No Telp" => "085812491175"
            ],
            [
                "No" => "23",
                "Nama" => "Pak Rofi",
                "Alamat" => "Jiwan Madiun",
                "No Telp" => ""
            ],
            [
                "No" => "24",
                "Nama" => "Pak Daman",
                "Alamat" => "Teguhan",
                "No Telp" => ""
            ],
            [
                "No" => "25",
                "Nama" => "Purnama Aluminium",
                "Alamat" => "Karasan"
            ]
        ];
        foreach ($dataPembeli as $pembeli) {
            Pembeli::create([
                'id_pembeli' => $pembeli['No'],
                'nama_pembeli' => $pembeli['Nama'],
                'alamat_pembeli' => $pembeli['Alamat'],
                'jenis_pembeli' => 'aplicator', // atau jenis pembeli yang lain
                'no_hp_pembeli' => isset($pembeli['No Telp']) ? $pembeli['No Telp'] : "",
            ]);
        }


        $stokBarangJson = public_path('closed/data/stokbarang.json');
        $stokBarang = json_decode(file_get_contents($stokBarangJson), true);
        foreach ($stokBarang as $dataStokBarang) {
            $tipeBarang = TipeBarang::firstOrCreate([
                'nama_tipe' => $dataStokBarang['Satuan'],
            ]);

            // Ambil id terakhir hari ini dari database
            $lastIdToday = Barang::max('id_barang');

            // Jika ada id terakhir hari ini, tambahkan 1, jika tidak, set id menjadi 1
            $nextId = $lastIdToday ? $lastIdToday + 1 : 1;

            // Format id dengan leading zero sepanjang 4 digit
            $nextIdFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $barang = Barang::create([

                'kode_barang' => 'BRG' . date('Ymd') . $nextIdFormatted,
                'nama_barang' => $dataStokBarang['Nama Barang'],
                'harga_barang' => intval(str_replace(['Rp', '.'], '',  $dataStokBarang[' Harga '])),
                'harga_barang_pemasok' => intval(str_replace(['Rp', '.'], '',  $dataStokBarang[' Harga '])) - 5000,
                'ukuran' => 'XL',
                // 'status_pembayaran' => 'hutang',
                'total' => 0.00,
                'nominal_terbayar' => 0.00,
                'tenggat_bayar' => null,
                'id_pemasok' => null, // Sesuaikan dengan id_pemasok yang sesuai jika ada
                'id_tipe_barang' => 1, // Sesuaikan dengan id_tipe_barang yang sesuai
            ]);


            // $barang->harga_barang_pemasok 
            $barang->total = $barang->harga_barang_pemasok * 30;
            $barang->nominal_terbayar = $barang->total;





            // Buat record baru untuk BukuBesar
            $bukuBesar = new BukubesarModel();

            $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
            $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
            $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
            $bukuBesar->keterangan = 'HUTANG STOK BARANG ' . $barang->id_barang . ' STOK- 30'; // Isi dengan keterangan yang sesuai
            $bukuBesar->debit = $barang->stok * $barang->harga_pemasok; // Isi dengan nilai kredit yang sesuai
            $bukuBesar->sub_kategori = 'hutang'; // Isi dengan sub kategori yang sesuai
            $bukuBesar->save();

            
            $stokBarang = StokBarangModel::create([
                'id_barang' =>  $barang->id_barang,
                'stok_masuk' => 30,
                'id_bukubesar' => $bukuBesar->id_bukubesar
            ]);
        }
    }
}
