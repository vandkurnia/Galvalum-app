<?php

namespace Database\Seeders;

use App\Models\BukubesarModel;
use App\Models\Notabukubesar;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pembeli::create( array('id_pembeli' => '1','hash_id_pembeli' => '9e96027b-7970-4c32-8ec3-c2b1396008dc','nama_pembeli' => 'Kucing','alamat_pembeli' => 'Alamat','no_hp_pembeli' => '0813031031031','created_at' => '2024-04-27 14:58:10','updated_at' => '2024-04-27 14:58:10','deleted_at' => NULL));
        NotaPembeli::create( array('id_nota' => '1','no_nota' => 'NT202404271457140001','id_pembeli' => '1','id_admin' => '1','metode_pembayaran' => 'CASH','status_pembayaran' => 'hutang','sub_total' => '220000','nominal_terbayar' => '20000.00','tenggat_bayar' => '2024-04-27','diskon' => '0','pajak' => '3000','total' => '217000','created_at' => '2024-04-27 14:58:10','updated_at' => '2024-04-27 14:58:10','deleted_at' => NULL));
        PesananPembeli::create(array('id_pesanan' => '1','jumlah_pembelian' => '1.00','harga' => '220000.00','diskon' => '0.00','id_nota' => '1','id_barang' => '3','jenis_pembelian' => "harga_normal",'id_diskon' => NULL,'created_at' => '2024-04-27 14:58:10','updated_at' => '2024-04-27 14:58:10','deleted_at' => NULL));
        BukubesarModel::create(array('id_bukubesar' => '1','hash_id_bukubesar' => 'fbc2ca13-4120-4958-a0a9-563d1f1938ec','id_akunbayar' => '1','tanggal' => '2024-04-27','kategori' => 'transaksi','keterangan' => 'NOTA NT202404271457140001 PIUTANG','debit' => '20000','kredit' => '0','sub_kategori' => 'piutang','created_at' => '2024-04-27 14:58:10','updated_at' => '2024-04-27 14:58:10','deleted_at' => NULL));
        Notabukubesar::create( array('id_notabukubesar' => '1','id_nota' => '1','id_bukubesar' => '1','created_at' => '2024-04-27 14:58:10','updated_at' => '2024-04-27 14:58:10'));
    }
}
