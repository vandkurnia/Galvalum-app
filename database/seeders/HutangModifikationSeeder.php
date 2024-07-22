<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\NotaPembeli;
use App\Models\RiwayatPiutangModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HutangModifikationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menghapus seluruh hutang dan menghapus nota
     *
     * @return void
     */
    public function run()
    {

        $notas = []; // Array untuk menyimpan id_nota

        // Mendapatkan seluruh NotaPembeli yang memiliki id_bukubesar null
        $notaPembeliData = DB::table('nota_pembelis')->whereNull('id_bukubesar')->get();
        foreach ($notaPembeliData as $notaPembeli) {


            // Menyimpan id_nota ke array



            $notas[] = [
                'id_nota' => $notaPembeli->id_nota,
                'no_nota' => $notaPembeli->no_nota,

            ];


            // dump($notaPembeli);

            $riwayatPiutangData = DB::table('riwayat_piutang')->where('id_nota', $notaPembeli->id_nota)->get();

            foreach ($riwayatPiutangData as $index => $riwayatPiutang) {

                if ($index == 0) {
                    // Cari yang dpnya tidak sama dengan 



                    DB::table('nota_pembelis')->where('id_nota', $riwayatPiutang->id_nota)->update(['id_bukubesar' => $riwayatPiutang->id_bukubesar]);
                    DB::table('riwayat_piutang')->where('id_piutang', $riwayatPiutang->id_piutang)->delete();

                    // if($notaPembeli->dp != $riwayatPiutang->nominal_dibayar)
                    // {
                    //     dd("tetew");
                    // }
                    // dump($notaPembeli->dp == $riwayatPiutang->nominal_dibayar);

                }
            }



            if (is_null($notaPembeli->id_bukubesar)) {
                $bukubesar = BukubesarModel::create([
                    'id_akunbayar' => 1,
                    'tanggal' => date('Y-m-d'),
                    'kategori' => 'transaksi',
                    'keterangan' => 'NOTA ' . $notaPembeli->no_nota,
                    'debit'  => $notaPembeli->dp,
                    'created_at' => $notaPembeli->created_at,
                    'updated_at' => $notaPembeli->updated_at

                ]);

                // Dapatkan ID dari entri baru di tabel bukubesar
                $id_bukubesar = $bukubesar->id_bukubesar;
                DB::table('nota_pembelis')->where('id_nota', $notaPembeli->id_nota)->update([
                    'id_bukubesar' => $id_bukubesar

                ]);
            }
        }



        // Mendapatkan barang lalu reset seluruh dp dan nominal terbayarnya
        $barangData = DB::table('barangs')->whereRaw('(dp_barang + nominal_terbayar) < total')->get();
        foreach ($barangData as $barang) {
            // Mendapatkan seluruh riwayat hutang terkait dengan barang ini
            $riwayatHutangData = DB::table('riwayat_hutang')->where('id_barang', $barang->id_barang)->get();

            // Hapus setiap riwayat hutang satu per satu
            foreach ($riwayatHutangData as $riwayatHutang) {
           
                
              
                DB::table('riwayat_hutang')->where('id', $riwayatHutang->id)->delete();
                DB::table('bukubesar')->where('id_bukubesar', $riwayatHutang->id_bukubesar)->delete();



            }
            // Reset dp_barang ke total_barang dan nominal_terbayar ke 0
            DB::table('barangs')
                ->where('id_barang', $barang->id_barang)
                ->update([
                    'dp_barang' => $barang->total,
                    'nominal_terbayar' => 0,
                    'stok_seluruh' => $barang->stok
                ]);
        }



        // Menyimpan array id_nota ke file JSON di direktori public
        $jsonContent = json_encode($notas, JSON_PRETTY_PRINT);
        file_put_contents('public/notanullidbukubesar.json', $jsonContent);
    }
}
