<?php

namespace Database\Seeders;

use App\Models\BukubesarModel;
use App\Models\HutangDanStokModel;
use App\Models\NotaPembeli;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrasiData6 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mengambil data dari tabel nota_pembelis dengan created_at kurang atau sama dengan 2024-08-24 dan id_bukubesar bernilai null
        $notaPembelis = DB::table('nota_pembelis')
            ->where('created_at', '<=', '2024-08-24')
            // ->whereNull('id_bukubesar')
            ->get();

        foreach ($notaPembelis as $notaPembeli) {
            // Mengambil notaPembeli dari database sql_galvalum_asli berdasarkan id_nota
            $notaPembeliGalvalum = DB::connection('sql_galvalum_asli')
                ->table('nota_pembelis')
                ->where('id_nota', $notaPembeli->id_nota)
                ->first();

            if ($notaPembeliGalvalum) {
                // Update kolom id_bukubesar di database utama dengan nilai dari database sql_galvalum_asli
                DB::table('nota_pembelis')
                    ->where('id_nota', $notaPembeli->id_nota)
                    ->update(['id_bukubesar' => $notaPembeliGalvalum->id_bukubesar]);
                echo "\nNota berhasil diupdate";
                DB::table('bukubesar')->where('id_bukubesar', $notaPembeliGalvalum->id_bukubesar)->update([
                    'debit' => $notaPembeli->dp

                ]);
                echo "\nBukubesar";
            }
        }



        // Hapus semua hutang
        $lacakStoks = DB::table('lacak_stok')
            ->whereRaw('total > dp + nominal_terbayar')
            ->get();

        // Iterasi setiap hasil dari lacak_stok
        foreach ($lacakStoks as $lacakStok) {
            // Cari dan hapus data dari riwayat_hutang yang sesuai dengan hutang_dan_stok_id
            DB::table('riwayat_hutang')
                ->where('hutang_dan_stok_id', $lacakStok->id_hutang_stok)
                ->delete();

            // Update nominal_terbayar jadi 0 dan dp samakan dengan total di lacak_stok
            DB::table('lacak_stok')
                ->where('id_hutang_stok', $lacakStok->id_hutang_stok)
                ->update([
                    'nominal_terbayar' => 0,
                    'dp' => $lacakStok->total
                ]);
        DB::table('bukubesar')->where('id_bukubesar', $lacakStok->id_bukubesar)->update([
            'debit' => $lacakStok->total

        ]);
        }

        // Hapus piutang yang diinginkan dan melunaskan
        $piutangDihapus = [
            [
                "nama" => "P. Budi",
                "nomor_transaksi" => "NT202405270159080413"
            ],
            [
                "nama" => "P. Sugeng",
                "nomor_transaksi" => "NT202405250455390402"
            ],
            [
                "nama" => "P. Mariono",
                "nomor_transaksi" => "NT202405230204030322"
            ],
            [
                "nama" => "Mba Umi",
                "nomor_transaksi" => "NT202405210040270277"
            ],
            [
                "nama" => "P. Syaifull",
                "nomor_transaksi" => "NT202405200220390241"
            ],
            [
                "nama" => "P. Aziz Pardi",
                "nomor_transaksi" => "NT202405121309420084"
            ]
        ];
        foreach ($piutangDihapus as $pdihapus) {
            $notaPiutangyngDihapus = DB::table('nota_pembelis')->where('no_nota', $pdihapus['nomor_transaksi'])->first();

            if ($notaPiutangyngDihapus) {
                $pelanggan = DB::table('pembelis')->where('id_pembeli', $notaPiutangyngDihapus->id_pembeli)->first();

                DB::table('riwayat_piutang')->where('id_nota', $notaPiutangyngDihapus->id_nota)->delete();


                DB::table('nota_pembelis')->where('id_nota', $notaPiutangyngDihapus->id_nota)->update([
                    'dp' => $notaPiutangyngDihapus->total,
                    'nominal_terbayar' => 0,
                    'tanggal_penyelesaian' => $notaPiutangyngDihapus->updated_at

                ]);

                DB::table('bukubesar')->where('id_bukubesar', $notaPiutangyngDihapus->id_bukubesar)->update([
                    'debit' => $notaPiutangyngDihapus->total

                ]);
            } else {
                dump($notaPiutangyngDihapus);
            }

            # code...
        }




        // Menghapus new Update
        // Cari data di bukubesar dengan keterangan yang mengandung pola "NOTA ... NEW UPDATE"
        $bukuBesarRecords = DB::table('bukubesar')
            ->where('keterangan', 'LIKE', 'NOTA % NEW UPDATE')
            ->get();

        // Hapus setiap record yang ditemukan
        foreach ($bukuBesarRecords as $record) {
            DB::table('bukubesar')
                ->where('id_bukubesar', $record->id_bukubesar)
                ->delete();
            
        }
    }
}
