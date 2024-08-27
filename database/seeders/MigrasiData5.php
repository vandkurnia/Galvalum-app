<?php

namespace Database\Seeders;

use App\Models\pdf\InvoicePembayaranModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrasiData5 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->command->info('=============================== Data Master ==================================');
        $users = DB::connection('sql_galvalum_asli')->table('users')->get();
        foreach ($users as $user) {
            DB::table('users')->insert([
                'hash_id_admin' => $user->hash_id_admin,
                'nama_admin' => $user->nama_admin,
                'no_telp_admin' => $user->no_telp_admin,
                'email_admin' => $user->email_admin,
                'password' => $user->password,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'deleted_at' => $user->deleted_at
            ]);
        }






        $this->command->info('Data user berhasil disimpan');


        $pembelis = DB::connection('sql_galvalum_asli')->table('pembelis')->get();
        foreach ($pembelis as $pembeli) {
            DB::table('pembelis')->insert([
                'id_pembeli' => $pembeli->id_pembeli,
                'hash_id_pembeli' => $pembeli->hash_id_pembeli,
                'nama_pembeli' => $pembeli->nama_pembeli,
                'alamat_pembeli' => $pembeli->alamat_pembeli,

                'jenis_pembeli' => $pembeli->jenis_pembeli,
                'no_hp_pembeli' => $pembeli->no_hp_pembeli,
                'created_at' => $pembeli->created_at,
                'updated_at' => $pembeli->updated_at,
                'deleted_at' => $pembeli->deleted_at

            ]);
        }

        $this->command->info('Data pembeli berhasil disimpan');

        $tipe_barangs = DB::connection('sql_galvalum_asli')->table('tipe_barangs')->get();
        foreach ($tipe_barangs as $tipe_barang) {
            DB::table('tipe_barangs')->insert([
                'id_tipe_barang' => $tipe_barang->id_tipe_barang,
                'hash_id_tipe_barang' => $tipe_barang->hash_id_tipe_barang,
                'nama_tipe' => $tipe_barang->nama_tipe,
                'created_at' => $tipe_barang->created_at,
                'updated_at' => $tipe_barang->updated_at,
                'deleted_at' => $tipe_barang->deleted_at

            ]);
        }
        $this->command->info('Data tipe barang berhasil disimpan');


        $bukubesars = DB::connection('sql_galvalum_asli')->table('bukubesar')->get();
        foreach ($bukubesars as $bukubesar) {
            // TipeBarang::create([
            //     'id_tipe_barang' => $tipe_barang->id_tipe_barang,
            //     'hash_id_tipe_barang' => $tipe_barang->hash_id_tipe_barang,
            //     'nama_tipe' => $tipe_barang->nama_tipe,
            //     'created_at' => $tipe_barang->created_at,
            //     'updated_at' => $tipe_barang->updated_at,
            //     'deleted_at' => $tipe_barang->deleted_at

            // ]);

            DB::table('bukubesar')->insert([
                'id_bukubesar' => $bukubesar->id_bukubesar,
                'hash_id_bukubesar' => $bukubesar->hash_id_bukubesar,
                'tanggal' => $bukubesar->tanggal,
                'kategori' => $bukubesar->kategori,
                'keterangan' => $bukubesar->keterangan,
                'debit' => $bukubesar->debit,
                'kredit' => $bukubesar->kredit,
                'created_at' => $bukubesar->created_at,
                'updated_at' => $bukubesar->updated_at,
                'deleted_at' => $bukubesar->deleted_at
            ]);
        }

        $this->command->info('Data bukubesar berhasil disimpan');
        $akun_bayar = DB::connection('sql_galvalum_asli')->table('akun_bayar')->get();
        foreach ($akun_bayar as $akunb) {
            DB::table('akun_bayar')->insert([
                'id_akunbayar' => $akunb->id_akunbayar,
                'hash_id_akunbayar' => $akunb->hash_id_akunbayar,
                'no_akun' => $akunb->no_akun,
                'nama_akun' => $akunb->nama_akun,
                'tipe_akun' => $akunb->tipe_akun,
                'saldo' => $akunb->saldo,
                'saldo_akhir' => $akunb->saldo_akhir,
                'saldo_anak' => $akunb->saldo_anak,
                'created_at' => $akunb->created_at,
                'updated_at' => $akunb->updated_at,
                'deleted_at' =>  $akunb->deleted_at
            ]);
        }

        $this->command->info('Data akun bayar berhasil disimpan');

        $diskon = DB::connection('sql_galvalum_asli')->table('diskon')->get();
        foreach ($diskon as $dskn) {
            DB::table('diskon')->insert([
                'id_diskon' => $dskn->id_diskon,
                'hash_id_diskon' => $dskn->hash_id_diskon,
                'kode_diskon' => $dskn->kode_diskon,
                'nama_diskon' => $dskn->nama_diskon,
                'type' => $dskn->type,
                'besaran' => $dskn->besaran,
                'status' => $dskn->status,
                'created_at' => $dskn->created_at,
                'updated_at' => $dskn->updated_at,
                'deleted_at' => $dskn->deleted_at

            ]);
        }

        $this->command->info('Data diskon berhasil disimpan');

        $pemasok_barangs = DB::connection('sql_galvalum_asli')->table('pemasok_barangs')->get();
        foreach ($pemasok_barangs as $pemasok_barang) {
            DB::table('pemasok_barangs')->insert([
                'id_pemasok' => $pemasok_barang->id_pemasok,
                'nama_pemasok' => $pemasok_barang->nama_pemasok,
                'hash_id_pemasok' => $pemasok_barang->hash_id_pemasok,
                'no_telp_pemasok' => $pemasok_barang->no_telp_pemasok,
                'alamat_pemasok' => $pemasok_barang->alamat_pemasok,
                'created_at' => $pemasok_barang->created_at,
                'updated_at' => $pemasok_barang->updated_at
            ]);
        }
        $this->command->info('Data pemasok berhasil disimpan');

        $barangs = DB::connection('sql_galvalum_asli')->table('barangs')->get();

        foreach ($barangs as $barang) {
            // // Membuat satu data bukubesar baru
            // $bukubesar = DB::table('bukubesar')->insert([
            //     'id_akunbayar' => 1, // Ganti dengan id_akunbayar yang sesuai
            //     'tanggal' => $barang->created_at, // Tanggal saat ini
            //     'kategori' => 'barang', // Misalnya kategori 'Penjualan'
            //     'keterangan' => "Tambah Stok Barang " . $barang->nama_barang, // Keterangan transaksi
            //     'debit' => $barang->nominal_terbayar, // Misalnya nominal debit
            //     // 'kredit' => 0, // Misalnya nominal kredit
            //     'created_at' => $barang->created_at,
            //     'updated_at' => $barang->updated_at,
            //     'deleted_at' => $barang->deleted_at
            // ]);
            DB::table('barangs')->insert([
                'hash_id_barang' => $barang->hash_id_barang,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'harga_barang' => $barang->harga_barang,
                'harga_barang_pemasok' => $barang->harga_barang_pemasok,
                'stok' => $barang->stok,
                'ukuran' => $barang->ukuran,
                'total' => $barang->total,
                // 'nominal_terbayar' => $barang->nominal_terbayar,
                'tenggat_bayar' => $barang->tenggat_bayar,
                'id_pemasok' => $barang->id_pemasok,
                'id_tipe_barang' => $barang->id_tipe_barang,
                // 'id_bukubesar' => $barang->id_bukubesar,
                'created_at' => $barang->created_at,
                'updated_at' => $barang->updated_at,
                'deleted_at' => $barang->deleted_at,
            ]);

            DB::table('lacak_stok')->insert([
                'harga_beli'       => $barang->harga_barang_pemasok, // Example value for harga_beli
                'total'            => $barang->total, // Example value for total
                'dp'               => $barang->dp_barang,  // Example value for dp
                'nominal_terbayar' => $barang->nominal_terbayar,  // Example value for nominal_terbayar
                'tenggat_waktu'    => $barang->tenggat_bayar ?? $barang->created_at, // Example value for tenggat_waktu
                'id_bukubesar'     => $barang->id_bukubesar,         // Example value for id_bukubesar (foreign key)
                'id_barang'        => $barang->id_barang,         // Example value for id_barang (foreign key)
                'stok'             => $barang->stok,     // Example value for stok
                'created_at'       => $barang->created_at,     // Use current timestamp for created_at
                'updated_at'       => $barang->updated_at,     // Use current timestamp for updated_at
                'deleted_at'       => $barang->deleted_at       // Set deleted_at to NULL
            ]);
        }

        $this->command->info('Data barang berhasil disimpan');
        $riwayatHutangs = DB::connection('sql_galvalum_asli')->table('riwayat_hutang')->get();
        foreach ($riwayatHutangs as $riwayatHutang) {



            $bukubesarHutang = DB::table('bukubesar')->where('id_bukubesar', $riwayatHutang->id_bukubesar)->first();
            $lacakStokPertama = DB::table('lacak_stok')->where('id_barang', $riwayatHutang->id_barang)->first();

            DB::table('riwayat_hutang')->insert([
                'id' => $riwayatHutang->id,
                'id_bukubesar' => $riwayatHutang->id_bukubesar ?? null,
                'id_barang' => $riwayatHutang->id_barang,
                'hutang_dan_stok_id' => $lacakStokPertama->id_hutang_stok,

                'nominal_dibayar' => $bukubesarHutang->debit,
                'created_at' => $riwayatHutang->created_at,
                'updated_at' => $riwayatHutang->updated_at
            ]);
        }
        $this->command->info('Data riwayat hutang berhasil disimpan');
        $this->command->info('=============================== Data Penjualan ==================================');
        $nota_pembelis = DB::connection('sql_galvalum_asli')->table('nota_pembelis')->get();
        $null_id_bukubesar = [];
        foreach ($nota_pembelis as $nota_pembeli) {

            // $nota_bukubesar_old = DB::connection('sql_galvalum_asli')->table('nota_bukubesar')->where('id_nota', $nota_pembeli->id_nota)->get();


            // $dp = 0;
            // $harga_nominal_terbayar = 0;
            // $tanggal_penyelesaian = null;

            // foreach ($nota_bukubesar_old as $index => $nota_bukubesar) {
            //     $bukubesar_nota_cicilan = DB::connection('sql_galvalum_asli')->table('bukubesar')->where('id_bukubesar', $nota_bukubesar->id_bukubesar)->first();
            //     if ($index == 0) {
            //         $dp = $bukubesar_nota_cicilan->debit;

            //         // $riwayatPiutangtoDelete = RiwayatPiutangModel::where('id_piutang', (int) $nota_bukubesar->id_notabukubesar)->first();
            //         // if ($riwayatPiutangtoDelete) {
            //         //     $riwayatPiutangtoDelete->delete();
            //         //     echo "Ok deleted";
            //         // } else {
            //         //     $null_id_bukubesar[] = [
            //         //         'id_notabukubesar' => $nota_bukubesar->id_notabukubesar,
            //         //         'riwayat_piutang' => $riwayatPiutangtoDelete
            //         //     ];
            //         // }
            //     } else {
            //         $harga_nominal_terbayar += $bukubesar_nota_cicilan->debit;
            //     }

            //     if (($dp + $harga_nominal_terbayar) == $nota_pembeli->total) {
            //         $tanggal_penyelesaian = $bukubesar_nota_cicilan->updated_at;
            //     }
            // }




            // if (!empty($null_id_bukubesar)) {
            //     $json_data = json_encode($null_id_bukubesar, JSON_PRETTY_PRINT);
            //     file_put_contents(public_path('notrecognizedpiutang.json'), $json_data);
            // }


            // Create a new bukubesar record
            // $bukuBesarPembelian = BukubesarModel::create([
            //     'id_akunbayar' => 1,
            //     'tanggal' => $nota_pembeli->created_at,
            //     'kategori' => 'transaksi',
            //     'keterangan' => 'NOTA ' . $nota_pembeli->no_nota,
            //     'debit' => $dp,
            //     'created_at' => $nota_pembeli->created_at,
            //     'updated_at' => $nota_pembeli->updated_at,
            //     'deleted_at' => $nota_pembeli->deleted_at
            // ]);


            // Insert nota_pembelis record with the new bukubesar id
            DB::table('nota_pembelis')->insert([
                'id_nota' => $nota_pembeli->id_nota,
                'no_nota' => $nota_pembeli->no_nota,
                'id_pembeli' => $nota_pembeli->id_pembeli,
                'id_admin' => $nota_pembeli->id_admin,
                'id_bukubesar' =>  $nota_pembeli->id_bukubesar,
                'metode_pembayaran' => $nota_pembeli->metode_pembayaran,
                'sub_total' => $nota_pembeli->sub_total,
                'nominal_terbayar' => $nota_pembeli->nominal_terbayar,
                'tenggat_bayar' => $nota_pembeli->tenggat_bayar,
                'diskon' => $nota_pembeli->diskon,
                'ongkir' => $nota_pembeli->ongkir,
                'total' => $nota_pembeli->total,
                'dp' => $nota_pembeli->dp,
                'tanggal_penyelesaian' => $nota_pembeli->tanggal_penyelesaian,
                'piutang_is_visible' => $nota_pembeli->piutang_is_visible,
                'created_at' => $nota_pembeli->created_at,
                'updated_at' => $nota_pembeli->updated_at,
                'deleted_at' => $nota_pembeli->deleted_at
            ]);
        }


        $this->command->info('Data nota pembeli berhasil disimpan');

        $pesanan_pembelis = DB::connection('sql_galvalum_asli')->table('pesanan_pembelis')->get();

        foreach ($pesanan_pembelis as $pesanan_pembeli) {
            DB::table('pesanan_pembelis')->insert([
                'jumlah_pembelian' => $pesanan_pembeli->jumlah_pembelian,
                'harga' => $pesanan_pembeli->harga,
                'diskon' => $pesanan_pembeli->diskon,
                'id_nota' => $pesanan_pembeli->id_nota,
                'id_barang' => $pesanan_pembeli->id_barang,
                'jenis_pembelian' => $pesanan_pembeli->jenis_pembelian,
                'harga_potongan' => $pesanan_pembeli->harga_potongan,
                'id_diskon' => $pesanan_pembeli->id_diskon,
                'created_at' => $pesanan_pembeli->created_at,
                'updated_at' => $pesanan_pembeli->updated_at,
                'deleted_at' => $pesanan_pembeli->deleted_at,
            ]);
        }




        $this->command->info('Data pesanan pembeli berhasil disimpan');        // 
        // 

        $riwayatPiutang = DB::connection('sql_galvalum_asli')->table('riwayat_piutang')->get();
        foreach ($riwayatPiutang as $riwayatPiutang) {



            $bukuBesarPiutang = DB::table('bukubesar')->where('id_bukubesar', $riwayatPiutang->id_bukubesar)->first();
            // }

            // dump($bukubesarPiutangIsExist);

            DB::table('riwayat_piutang')->insert([
                'id_piutang' => $riwayatPiutang->id_piutang,
                'id_nota' => $riwayatPiutang->id_nota,
                'id_bukubesar' => $riwayatPiutang->id_bukubesar ?? null,
                // 'nominal_dibayar' => $bukuBesarPiutang->debit ? $bukuBesarPiutang->debit : 0,
                'nominal_dibayar' => $riwayatPiutang->nominal_dibayar,
                'created_at' => $riwayatPiutang->created_at,
                'updated_at' => $riwayatPiutang->updated_at
            ]);
        }
        $this->command->info('Data piutang berhasil disimpan');




        $surat_jalans = DB::connection('sql_galvalum_asli')->table('surat_jalan')->get();

        foreach ($surat_jalans as $surat_jalan) {
            DB::table('surat_jalan')->insert([
                'id_surat_jalan' => $surat_jalan->id_surat_jalan,
                'no_surat_jalan' => $surat_jalan->no_surat_jalan,
                'users' => $surat_jalan->users,
                'id_nota' => $surat_jalan->id_nota,
                'created_at' => $surat_jalan->created_at,
                'updated_at' => $surat_jalan->updated_at,
                'deleted_at' => $surat_jalan->deleted_at
            ]);
        }

        $this->command->info('Data surat jalan berhasil disimpan');


        $invoice_pembelians = DB::connection('sql_galvalum_asli')->table('invoice_pembelian')->get();

        foreach ($invoice_pembelians as $invoice_pembelian) {

            DB::table('invoice_pembelian')->insert([
                'id' => $invoice_pembelian->id,
                'users' => $invoice_pembelian->users,
                'id_nota' => $invoice_pembelian->id_nota,
                'created_at' => $invoice_pembelian->created_at,
                'updated_at' => $invoice_pembelian->updated_at
            ]);
        }

        $this->command->info('Data invoice pembelian berhasil disimpan');

        $this->command->info('=============================== Data Retur ==================================');
        $retur_pemasoks = DB::connection('sql_galvalum_asli')->table('retur_pemasok')->get();

        foreach ($retur_pemasoks as $retur_pemasok) {
            DB::table('retur_pemasok')->insert([
                'id_retur_pemasok' => $retur_pemasok->id_retur_pemasok,
                'hash_id_retur_pemasok' => $retur_pemasok->hash_id_retur_pemasok,
                'no_retur_pemasok' => $retur_pemasok->no_retur_pemasok,
                'tanggal_retur' => $retur_pemasok->tanggal_retur,
                'bukti_retur_pemasok' => $retur_pemasok->bukti_retur_pemasok, // Ubah path sesuai file yang ada
                'jenis_retur' => $retur_pemasok->jenis_retur,
                'total_nilai_retur' => $retur_pemasok->total_nilai_retur,
                'pengembalian_data' => $retur_pemasok->pengembalian_data,
                'kekurangan' => $retur_pemasok->kekurangan,
                'harga' => $retur_pemasok->harga,
                'total' => $retur_pemasok->total,
                'qty' => $retur_pemasok->qty,
                'qty_sebelum_perubahan' => $retur_pemasok->qty_sebelum_perubahan,
                'type_retur_pesanan' => $retur_pemasok->type_retur_pesanan,
                'status' => $retur_pemasok->status,
                'id_pemasok' => $retur_pemasok->id_pemasok, // Pastikan ID ini ada di tabel pemasok_barangs
                'id_barang' => $retur_pemasok->id_barang,  // Pastikan ID ini ada di tabel barangs
                'created_at' => $retur_pemasok->created_at,
                'updated_at' => $retur_pemasok->updated_at,
                'deleted_at' => $retur_pemasok->deleted_at
            ]);
        }
        $this->command->info('Data retur pemasok berhasil disimpan');



        $retur_pembelis = DB::connection('sql_galvalum_asli')->table('retur_pembeli')->get();

        foreach ($retur_pembelis as $retur_pembeli) {

            // // Get all records and retrieve the last one
            // $notaPembeliReturGet = NotaPembeli::all();
            // $lastNotaPembeli = $notaPembeliReturGet->last();

            // Find the specific record based on the provided id_nota
            $notaPembeliRetur = DB::table('nota_pembelis')->where('id_nota', $retur_pembeli->id_nota)->first();

            // // Debug the last record and the specific record
            // dd([$notaPembeliRetur, $lastNotaPembeli, $retur_pembeli->id_nota]);


            // Untuk data yang diinputkan sebelum tanggal 15 dp beforenya mengikuti sebelumnya 
            DB::table('retur_pembeli')->insert([
                'id_retur_pembeli' => $retur_pembeli->id_retur_pembeli,
                'hash_id_retur_pembeli' => $retur_pembeli->hash_id_retur_pembeli,
                'id_nota' => $retur_pembeli->id_nota, // Pastikan kolom ini ada di database lama
                'no_retur_pembeli' => $retur_pembeli->no_retur_pembeli, // Pastikan kolom ini ada di database lama
                'faktur_retur_pembeli' => $retur_pembeli->faktur_retur_pembeli, // Pastikan kolom ini ada di database lama
                'tanggal_retur_pembeli' => $retur_pembeli->tanggal_retur_pembeli, // Asumsikan ini sama dengan tanggal_retur_pembeli
                'bukti_retur_pembeli' => $retur_pembeli->bukti_retur_pembeli, // Pastikan kolom ini ada di database lama
                'jenis_retur' => $retur_pembeli->jenis_retur, // Asumsikan ini sama dengan jenis_retur
                'total_nilai_retur' => $retur_pembeli->total_nilai_retur ?? 0, // Default ke 0 jika null
                'pengembalian_data' => $retur_pembeli->pengembalian_data ?? 0, // Default ke 0 jika null
                'kekurangan' => $retur_pembeli->kekurangan ?? 0, // Default ke 0 jika null
                'status' => $retur_pembeli->status, // Asumsikan ini sama dengan status
                'dp_before' => $notaPembeliRetur->dp,
                'tanggal_penyelesaian_before' => $notaPembeliRetur->tanggal_penyelesaian,
                'id_pembeli' => $retur_pembeli->id_pembeli,
                'created_at' => $retur_pembeli->created_at, // Tambahkan timestamp jika diperlukan
                'updated_at' => $retur_pembeli->updated_at, // Tambahkan timestamp jika diperlukan
                'deleted_at' => $retur_pembeli->deleted_at // Pastikan soft delete diatur ke null
            ]);
        }
        $this->command->info('Data retur pembeli berhasil disimpan');


        $retur_pesanan_pembelis = DB::connection('sql_galvalum_asli')->table('retur_pesanan_pembeli')->get();

        foreach ($retur_pesanan_pembelis as $retur_pesanan_pembeli) {

            $pesananPembeliRetur = DB::table('pesanan_pembelis')
                ->where('id_pesanan', $retur_pesanan_pembeli->id_pesanan_pembeli)
                ->first();

            DB::table('retur_pesanan_pembeli')->insert([
                'id_retur_pesanan' => $retur_pesanan_pembeli->id_retur_pesanan,
                'id_retur_pembeli' => $retur_pesanan_pembeli->id_retur_pembeli, // Pastikan kolom ini ada di database lama
                'id_pesanan_pembeli' => $retur_pesanan_pembeli->id_pesanan_pembeli, // Pastikan kolom ini ada di database lama
                'harga' => $retur_pesanan_pembeli->harga, // Pastikan kolom ini ada di database lama
                'total' => $retur_pesanan_pembeli->total, // Pastikan kolom ini ada di database lama
                'qty' => $retur_pesanan_pembeli->qty, // Asumsikan jumlah_retur adalah qty
                'qty_sebelum_perubahan' => $retur_pesanan_pembeli->qty_sebelum_perubahan, // Default ke null jika tidak ada
                'type_retur_pesanan' => $retur_pesanan_pembeli->type_retur_pesanan, // Pastikan kolom ini ada di database lama

                // 2 Kolom Baru
                'jenis_pembelian_sebelumnya' => $pesananPembeliRetur->jenis_pembelian,
                'harga_potongan_sebelumnya' => $pesananPembeliRetur->harga_potongan,


                'created_at' => $retur_pesanan_pembeli->created_at, // Tambahkan timestamp jika diperlukan
                'updated_at' => $retur_pesanan_pembeli->updated_at, // Tambahkan timestamp jika diperlukan
                'deleted_at' => $retur_pesanan_pembeli->deleted_at // Pastikan soft delete diatur ke null
            ]);
        }

        $this->command->info('Data retur pesanan pembeli berhasil disimpan');

        $this->command->info('=============================== Data Log ==================================');

        $log_notas = DB::connection('sql_galvalum_asli')->table('log_nota')->get();
        foreach ($log_notas as $log_nota) {
            DB::table('log_nota')->insert([
                'id' => $log_nota->id,
                'json_content' => $log_nota->json_content,
                'tipe_log' => $log_nota->tipe_log,
                'keterangan' => $log_nota->keterangan,
                'id_admin' => $log_nota->id_admin,
                'id_nota' => $log_nota->id_nota,
                'created_at' => $log_nota->created_at,
                'updated_at' => $log_nota->updated_at,

            ]);
        }

        $this->command->info('Data log nota berhasil disimpan');
        $stok_barang_historys = DB::connection('sql_galvalum_asli')->table('stok_barang_history')->get();

        foreach ($stok_barang_historys as $stok_barang_history) {
            DB::table('stok_barang_history')->insert([
                'id_stok' => $stok_barang_history->id_stok,
                'id_barang' => $stok_barang_history->id_barang,
                'stok_masuk' => $stok_barang_history->stok_masuk,
                'stok_keluar' => $stok_barang_history->stok_keluar,
                'stok_terkini' => $stok_barang_history->stok_terkini,
                'created_at' => $stok_barang_history->created_at,
                'updated_at' => $stok_barang_history->updated_at
            ]);
        }
        $this->command->info('Data stok barang history berhasil disimpan');

        $log_stok_barangs = DB::connection('sql_galvalum_asli')->table('log_stok_barang')->get();
        foreach ($log_stok_barangs as $log_stok_barang) {
            DB::table('log_stok_barang')->insert([
                'id' => $log_stok_barang->id,
                'json_content' => $log_stok_barang->json_content,
                'tipe_log' => $log_stok_barang->tipe_log,
                'keterangan' => $log_stok_barang->keterangan,
                'id_admin' => $log_stok_barang->id_admin,
                'id_barang' => $log_stok_barang->id_barang,
                'id_stok_barang_history' => $log_stok_barang->id_stok_barang_history,
                'created_at' => $log_stok_barang->created_at,
                'updated_at' => $log_stok_barang->updated_at
            ]);
        }
        $this->command->info('Data log stok barang berhasil disimpan');
    }
}
