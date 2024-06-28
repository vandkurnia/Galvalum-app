<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\DiskonModel;
use App\Models\Log\LogNotaModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\RiwayatPiutangModel;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use App\Models\StokBarangHistoryModel;
// use App\Models\StokBarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function edit(Request $request, $id)
    {
        $notaPembelian = NotaPembeli::where('id_nota', $id)->with('Pembeli', 'PesananPembeli')->first();
        $dataPesanan = PesananPembeli::where('id_nota', $notaPembelian->id_nota)->with('Barang', 'Barang.TipeBarang')->get();
        $dataDiskon = DiskonModel::all();

        return view('daftar_transaksi.edit', compact('notaPembelian', 'dataPesanan', 'dataDiskon'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'jenis_pelanggan' => 'required|string',
            'status_pembelian' => 'required|string',
            'id_pembeli' => 'required|string',
            'pesanan' => 'required',
            'no_nota' => 'required',
            'nominal_terbayar' => 'required',
            // 'tenggat_bayar' => 'required',
            'total_ongkir' => 'required',
            'diskon' => 'required'


        ]);
        // dd($request->all());
        DB::beginTransaction();
        // dd($request->get('pesanan'));
        $pesananData = json_decode($request->get('pesanan'), true);
        // Get Data pembeli
        $pembeliData = Pembeli::firstOrCreate(
            ['hash_id_pembeli' => $request->get('id_pembeli')],
            [
                'nama_pembeli' => $request->id_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'no_hp_pembeli' => $request->no_hp,
                'jenis_pembeli' => $request->get('jenis_pelanggan')

            ] // Isi dengan data default jika pembeli baru dibuat
        );

        $notaPembeli = new NotaPembeli;
        $notaPembeli->no_nota = $request->get('no_nota');
        // $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        // $notaPembeli->status_pembayaran = $request->get('status_pembelian'); // Contoh nilai untuk status_pembelian
        $notaPembeli->metode_pembayaran = $request->get('metode_pembayaran');
        $notaPembeli->id_pembeli = $pembeliData->id_pembeli; // Contoh nilai untuk id_pembeli
        $notaPembeli->id_admin = Auth::id(); // Contoh nilai untuk id_admin
        $notaPembeli->nominal_terbayar =  $request->get('nominal_terbayar');
        $notaPembeli->tenggat_bayar = $request->get('tenggat_bayar');
        // Nominal Terbayar
        $notaPembeli->dp = $request->dp ?? 0;

 

    
        $notaPembeli->save();




        // Sub Total seluruhnya 
        $subTotal = 0;
        $totalDiskon = 0;
        // Perulangan untuk pesanan
        foreach ($pesananData as $pesanan) {
            // Data barang 
            $barangData = Barang::with('stokBarang')->where('hash_id_barang', $pesanan['id_barang'])->first();
            if (empty($barangData)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan barang, barang tidak ada');
            }
            $diskon = DiskonModel::where('hash_id_diskon', $pesanan['id_diskon'])->first();
            $diskonId = $diskon ? $diskon->id_diskon : null;
            // Inisiasi diskon
            $amountDiskon = isset($diskon->amount) ? $diskon->amount : 0;

            $typeDiskon = $diskon->type ?? 'amount'; // Jika $diskon->type tidak ada, maka gunakan 'amount'

            $amountDiskon = $diskon->amount ?? 0; // Jika $diskon->amount tidak ada, maka gunakan 0



            $hargaSetelahDiskon = 0;
            if ($typeDiskon == "percentage") {
                $jumlahDiskon = ($barangData->harga_barang * $amountDiskon) / 100;
                $hargaDiskon = $jumlahDiskon;
                $hargaSetelahDiskon = $barangData->harga_barang - $hargaDiskon;
            } else {
                $hargaDiskon = $amountDiskon;
                $hargaSetelahDiskon = $barangData->harga_barang - $hargaDiskon;
            }


            $hargaSetelahDiskon = $hargaSetelahDiskon - $pesanan['harga_potongan'];
            $subTotal += $hargaSetelahDiskon *  $pesanan['jumlah_pesanan'];
            $totalDiskon += $hargaDiskon;



            // Membuat Pesanan Pembeli
            $pesananPembeli = new PesananPembeli;
            $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
            $pesananPembeli->id_diskon = $diskonId;
            $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
            $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
            $pesananPembeli->harga = $hargaSetelahDiskon;
            $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
            $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
            $pesananPembeli->diskon = $hargaDiskon;

            // Array data user dari request


            // Update data barang

            // $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();


            if ($barangData->stok >=  $pesanan['jumlah_pesanan']) {



                // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                // $barangData->save();
                // $stokBarang = StokBarangModel::create([
                //     'stok_keluar' => $pesanan['jumlah_pesanan'],
                //     'id_barang' => $barangData->id_barang,
                //     'tipe_stok' => 'pesanan'
                // ]);


                $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                $barangData->save();

                // Buat instance dari model
                $stokbarangHistory = new StokBarangHistoryModel();
                $stokbarangHistory->id_barang = $barangData->id_barang;
                // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                $stokbarangHistory->stok_keluar = $pesanan['jumlah_pesanan'];
                $stokbarangHistory->stok_terkini = $barangData->stok;
                $stokbarangHistory->save();

                // Simpan ke log
                $logStokBarang = new LogStokBarangModel();
                $logStokBarang->json_content = [
                    'type' => 'pembelian_store',
                    'data' => [
                        'no_nota' => $notaPembeli->no_nota,
                        'stok_keluar' => $pesanan['jumlah_pesanan'],
                        'pelanggan' => $pembeliData->id_pelanggan
                    ]
                ]; // Sesuaikan dengan isi json_content Anda
                $logStokBarang->tipe_log = 'pesanan_create';
                $logStokBarang->keterangan = 'Pesanan nota ' . $notaPembeli->no_nota . ' stok keluar sebanyak ' . $pesanan['jumlah_pesanan'] . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                $logStokBarang->id_barang = $barangData->id_barang; // Sesuaikan dengan id_barang yang ada
                $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                $logStokBarang->save();
                // Pindah ke PesananPembeli
                // $pesananPembeli->id_stokbarang = $stokBarang->id;
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi Kesalahan');
            }
            $pesananPembeli->save();
        }




        // Old
        // $updateNotaPembeli = NotaPembeli::with('bukuBesar')->find($notaPembeli->id_nota);
        // New
        $updateNotaPembeli = NotaPembeli::find($notaPembeli->id_nota);

        $updateNotaPembeli->sub_total = $subTotal;
        $updateNotaPembeli->diskon = $request->get('diskon');
        $updateNotaPembeli->ongkir = $request->get('total_ongkir');

        // Perhitungan Pajak
        $nilaiTotal = $updateNotaPembeli->sub_total - $updateNotaPembeli->diskon;
        // $nilaiPajak = $nilaiTotal * ( $updateNotaPembeli->pajak / 100);
        $nilaiOngkir =  $updateNotaPembeli->ongkir;
        $updateNotaPembeli->total = $nilaiTotal + $nilaiOngkir;
        $updateNotaPembeli->save();


        // Jika Langsung lunas maka tanggal penyelesaiannya hari itu juga jika hutang maka tanggal penyelesaian null
        if ($updateNotaPembeli->total == ($updateNotaPembeli->nominal_terbayar + $updateNotaPembeli->dp)) {
            $updateNotaPembeli->tanggal_penyelesaian = $updateNotaPembeli->created_at;
            $updateNotaPembeli->save();
        }

        // dump([
        //     'total' => $updateNotaPembeli
        // ]);
        // Old Bukubesar
        // Membuat satu data baru
        // $bukuBesarPembelian = new BukubesarModel();
        // $bukuBesarPembelian->id_akunbayar = 1;
        // $bukuBesarPembelian->tanggal = date('Y-m-d'); // Tanggal saat ini
        // $bukuBesarPembelian->kategori = 'transaksi';
        // $bukuBesarPembelian->keterangan = 'NOTA ' . $notaPembeli->no_nota; // Ganti dengan keterangan yang sesuai
        // $bukuBesarPembelian->debit = $updateNotaPembeli->nominal_terbayar; // Misalnya debit sebesar 1000
        // $bukuBesarPembelian->save();
        // $updateNotaPembeli->bukuBesar()->save($bukuBesarPembelian);






        $bukuBesarPembelian = new BukubesarModel();
        $bukuBesarPembelian->id_akunbayar = 1;
        $bukuBesarPembelian->tanggal = date('Y-m-d'); // Tanggal saat ini
        $bukuBesarPembelian->kategori = 'transaksi';
        $bukuBesarPembelian->keterangan = 'NOTA ' . $notaPembeli->no_nota; // Ganti dengan keterangan yang sesuai
        $bukuBesarPembelian->debit = $updateNotaPembeli->nominal_terbayar; // Misalnya debit sebesar 1000
        $bukuBesarPembelian->save();
        // $updateNotaPembeli->bukuBesar()->save($bukuBesarPembelian);


        // Update bukubesar
        $updateNotaPembeli->id_bukubesar = $bukuBesarPembelian->id_bukubesar;
        $updateNotaPembeli->save();







        // Log Nota
        // Asumsikan $notaPembeli adalah instance dari model NotaPembeli yang sudah ada
        $notaPembeliToSave = NotaPembeli::with('PesananPembeli')->find($notaPembeli->id_nota)->toArray();
        $logNota = new LogNotaModel();
        $logNota->json_content = $notaPembeliToSave;
        $logNota->tipe_log = 'create';
        $logNota->keterangan = 'Membuat Nota Pembeli baru';
        $logNota->id_nota = $notaPembeli->id_nota;
        $logNota->id_admin = Auth::user()->id_admin; // Mengambil id_admin dari user yang sedang login
        $logNota->save();



        DB::commit();
        // dump($request->all());
        // dd("berhasil");

        return redirect()->route('pemesanan.index')->with('success', 'Pesanan herhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {



        $request->validate([
            'metode_pembayaran' => 'required|string',
            'id_pembeli' => 'required',
            // 'jenis_pembelian' => 'required|string|in:harga_normal,aplicator,potongan', // Assuming these are the possible values
            'pesanan' => 'required|json',
            'total_ongkir' => 'required|integer',
            'diskon' => 'required|integer',
            'nominal_terbayar' => 'required'
        ]);

        DB::beginTransaction();
        // Get Data pembeli
        $pembeliData = Pembeli::firstOrCreate(
            ['hash_id_pembeli' => $request->get('id_pembeli')],
            [
                'nama_pembeli' => $request->id_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'no_hp_pembeli' => $request->no_hp,
            ] // Isi dengan data default jika pembeli baru dibuat
        );

        $notaPembeli = NotaPembeli::where('id_nota', $id)->first();

        if (!$notaPembeli) {
            return redirect()->back()->with(['error' => 'Tidak ada nota pembeli']);
        }
        // $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        $notaPembeli->no_nota = $request->get('no_nota');
        // $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        // $notaPembeli->status_pembayaran = $request->get('status_pembelian'); // Contoh nilai untuk status_pembelian
        $notaPembeli->metode_pembayaran = $request->get('metode_pembayaran');
        $notaPembeli->id_pembeli = $pembeliData->id_pembeli; // Contoh nilai untuk id_pembeli
        $notaPembeli->id_admin = Auth::id(); // Contoh nilai untuk id_admin
        // Nominal Terbayar
        $totalOld = $notaPembeli->total;
        $nominalTerbayarOld =  $notaPembeli->nominal_terbayar;
        $notaPembeli->nominal_terbayar = $request->nominal_terbayar;
  
        $notaPembeli->tenggat_bayar = $request->tenggat_bayar ?? $notaPembeli->tenggat_bayar;
     
        $notaPembeli->dp = $request->dp ?? 0;


        // Nominal Terbayar
        $notaPembeli->save();


        // Sub Total seluruhnya 
        // $subTotal = 0;
        // $totalDiskon = 0;
        $pesananData = json_decode($request->pesanan, true);
        // Perulangan untuk pesanan
        foreach ($pesananData as $pesanan) {
            // Data barang 
            $barangData = Barang::with('stokBarang')->where('hash_id_barang', $pesanan['id_barang'])->first();
            if (empty($barangData)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan barang, barang tidak ada');
            }
            $diskon = DiskonModel::where('hash_id_diskon', $pesanan['id_diskon'])->first();
            $diskonId = $diskon ? $diskon->id_diskon : null;
            // Inisiasi diskon
            $amountDiskon = isset($diskon->amount) ? $diskon->amount : 0;

            $typeDiskon = $diskon->type ?? 'amount'; // Jika $diskon->type tidak ada, maka gunakan 'amount'

            $amountDiskon = $diskon->amount ?? 0; // Jika $diskon->amount tidak ada, maka gunakan 0



            $hargaSetelahDiskon = 0;
            if ($typeDiskon == "percentage") {
                $jumlahDiskon = ($barangData->harga_barang * $amountDiskon) / 100;
                $hargaDiskon = $jumlahDiskon;
                $hargaSetelahDiskon = $barangData->harga_barang - $hargaDiskon;
            } else {
                $hargaDiskon = $amountDiskon;
                $hargaSetelahDiskon = $barangData->harga_barang - $hargaDiskon;
            }


            $hargaSetelahDiskon = $hargaSetelahDiskon - $pesanan['harga_potongan'];
            // $subTotal += $hargaSetelahDiskon *  $pesanan['jumlah_pesanan'];
            // $totalDiskon += $hargaDiskon;






            // Data ada tetapi ada perubahan
            if ($pesanan['type_pesanan'] == 'exist') {
                // // Pesanan Pembeli
                $pesananPembeli = PesananPembeli::where('id_nota', $notaPembeli->id_nota)->where('id_barang', $barangData->id_barang)->first();
                // $jumlahStokOld = $pesananPembeli->jumlah_pembelian;
                // $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                // $pesananPembeli->id_diskon = $diskonId;
                // $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                // $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                // $pesananPembeli->harga = $hargaSetelahDiskon;
                // $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
                // $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
                // $pesananPembeli->diskon = $hargaDiskon;



                // $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                // if (($stokTersedia->stok + $jumlahStokOld) >=  $pesanan['jumlah_pesanan']) {



                //     // Cari StokBarang termasuk yang sudah dihapus
                //     $stokBarang = StokBarangModel::withTrashed()->find($pesananPembeli->id_stokbarang);

                //     if ($stokBarang) {
                //         $stokKeluarLama = $stokBarang->stok_keluar;
                //         $stokBarang->stok_keluar = $pesananPembeli->jumlah_pembelian;
                //         $stokBarang->id_barang = $barangData->id_barang;

                //         // Restore jika stok_keluar > 0 dan stokBarang dalam keadaan terhapus
                //         if ($stokBarang->stok_keluar > 0 && $stokBarang->trashed()) {
                //             $stokBarang->restore();
                //         }

                //         $stokBarang->save();




                //         // Simpan ke log
                //         $logStokBarang = new LogStokBarangModel();
                //         $logStokBarang->json_content = $stokBarang; // Sesuaikan dengan isi json_content Anda
                //         $logStokBarang->tipe_log = 'pesanan_update';
                //         $logStokBarang->keterangan = 'Update pesanan qty dari ' . $stokKeluarLama . ' ke ' . $stokBarang->stok_keluar . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                //         $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                //         $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                //         $logStokBarang->id_barang = $stokBarang->id_barang; // Sesuaikan dengan id_barang yang ada
                //         $logStokBarang->save();
                //     } else {
                //         // Handle the case where the stock entry does not exist
                //         return redirect()->back()->with('error', 'Stock entry not found for this order.');
                //     }
                // } else {
                //     DB::rollBack();
                //     return redirect()->back()->with('error', 'Terjadi Kesalahan: Stok Tidak Terseida');
                // }
                // $pesananPembeli->save();


                // Cek Jika pesanan yang sudah ada mau dihapus
                if ($pesanan['terhapus'] == 'yes') {
                    // Hapus Pesanan dan Stok
                    $pesananPembelidelete = PesananPembeli::find($pesananPembeli->id_pesanan);
                    $jumlahPembelian = $pesananPembelidelete->jumlah_pembelian;
                    $barangData->stok = $barangData->stok + $jumlahPembelian;
                    $barangData->save();

                    $pesananPembelidelete->delete();
                    // $stokBarangdelete = StokBarangModel::find($pesananPembelidelete->id_stokbarang);




                    // $stokBarangdelete->delete();




                    $stokbarangHistory = new StokBarangHistoryModel();
                    $stokbarangHistory->id_barang = $barangData->id_barang;
                    $stokbarangHistory->stok_masuk = $jumlahPembelian;

                    $stokbarangHistory->stok_terkini = $barangData->stok;
                    $stokbarangHistory->save();


                    // Simpan ke log
                    $logStokBarang = new LogStokBarangModel();
                    $logStokBarang->json_content = [
                        'type' => 'pembelian_store',
                        'data' => [
                            'no_nota' => $notaPembeli->no_nota,
                            'stok_keluar' => $pesanan['jumlah_pesanan'],
                            'pelanggan' => $pembeliData->id_pelanggan
                        ]
                    ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                    $logStokBarang->tipe_log = 'pesanan_update';
                    $logStokBarang->keterangan = 'Hapus pesanan awal  dari nota ' . $notaPembeli->no_nota . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                    $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                    // $logStokBarang->id_stok_barang = $stokBarangdelete->id; // Sesuaikan dengan id_stok_barang yang ada
                    $logStokBarang->id_barang = $barangData->id_barang; // Sesuaikan dengan id_barang yang ada
                    $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    $logStokBarang->save();
                }
            } elseif ($pesanan['type_pesanan'] == 'modified') {
                // Pesanan Pembeli
                $pesananPembeli = PesananPembeli::where('id_nota', $notaPembeli->id_nota)->where('id_barang', $barangData->id_barang)->first();
                $jumlahStokOld = $pesananPembeli->jumlah_pembelian;
                $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                $pesananPembeli->id_diskon = $diskonId;
                $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                $pesananPembeli->harga = $hargaSetelahDiskon;
                $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
                $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
                $pesananPembeli->diskon = $hargaDiskon;
                $pesananPembeli->save();

                // $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                $stokTersedia = $barangData;
                if (($stokTersedia->stok + $jumlahStokOld) >=  $pesanan['jumlah_pesanan']) {



                    // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                    // $barangData->save();
                    // Cari StokBarang termasuk yang sudah dihapus
                    // $stokBarang = StokBarangModel::withTrashed()->find($pesananPembeli->id_stokbarang);

                    // if ($stokBarang) {
                    //     $stokKeluarLama = $stokBarang->stok_keluar;
                    //     $stokBarang->stok_keluar = $pesananPembeli->jumlah_pembelian;
                    //     $stokBarang->id_barang = $barangData->id_barang;

                    //     // Restore jika stok_keluar > 0 dan stokBarang dalam keadaan terhapus
                    //     if ($stokBarang->stok_keluar > 0 && $stokBarang->trashed()) {
                    //         $stokBarang->restore();
                    //     }

                    //     $stokBarang->save();






                    //     $perbedaan =  $pesanan['jumlah_pesanan'] - $jumlahStokOld;
                    //     $barangData->stok = $barangData->stok - $perbedaan;
                    //     $barangData->save();
                    //     $stokbarangHistory = new StokBarangHistoryModel();
                    //     $stokbarangHistory->id_barang = $barangData->id_barang;
                    //     // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                    //     $stokbarangHistory->stok_keluar = $perbedaan;
                    //     $stokbarangHistory->stok_terkini = $barangData->stok;
                    //     $stokbarangHistory->save();

                    //     // Simpan ke log
                    //     $logStokBarang = new LogStokBarangModel();
                    //     $logStokBarang->json_content = $stokBarang; // Sesuaikan dengan isi json_content Anda
                    //     $logStokBarang->tipe_log = 'pesanan_update';
                    //     $logStokBarang->keterangan = 'Update pesanan qty dari ' . $stokKeluarLama . ' ke ' . $stokBarang->stok_keluar  . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                    //     $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                    //     $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                    //     $logStokBarang->id_barang = $stokBarang->id_barang; // Sesuaikan dengan id_barang yang ada
                    //     $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    //     $logStokBarang->save();
                    // } else {
                    //     // Handle the case where the stock entry does not exist
                    //     return redirect()->back()->with('error', 'Stock entry not found for this order.');
                    // }



                    $perbedaan =  $pesanan['jumlah_pesanan'] - $jumlahStokOld;
                    $barangData->stok = $barangData->stok - $perbedaan;
                    $barangData->save();
                    $stokbarangHistory = new StokBarangHistoryModel();
                    $stokbarangHistory->id_barang = $barangData->id_barang;
                    // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                    $stokbarangHistory->stok_keluar = $perbedaan;
                    $stokbarangHistory->stok_terkini = $barangData->stok;
                    $stokbarangHistory->save();

                    // Simpan ke log
                    $logStokBarang = new LogStokBarangModel();
                    $logStokBarang->json_content = [
                        'type' => 'pembelian_store',
                        'data' => [
                            'no_nota' => $notaPembeli->no_nota,
                            'stok_keluar' =>  $jumlahStokOld,
                            'stok_masuk' => 0,
                            'pelanggan' => $pembeliData->id_pelanggan
                        ]
                    ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda; // Sesuaikan dengan isi json_content Anda
                    $logStokBarang->tipe_log = 'pesanan_update';
                    $logStokBarang->keterangan = 'Update pesanan qty dari ' .  $jumlahStokOld . ' ke ' . $pesananPembeli->jumlah_pembelian  . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                    $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                    // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                    $logStokBarang->id_barang = $barangData->id_barang; // Sesuaikan dengan id_barang yang ada
                    $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    $logStokBarang->save();
                } else {
                    DB::rollBack();
                    $stokTersediaSaatIni = $stokTersedia->stok;
                    return redirect()->back()->with('error', "Terjadi Kesalahan : Stok Tersedia {$stokTersediaSaatIni}  ");
                }



                // Cek Jika pesanan yang sudah ada mau dihapus
                if ($pesanan['terhapus'] == 'yes') {
                    // Hapus Pesanan dan Stok
                    $pesananPembelidelete = PesananPembeli::find($pesananPembeli->id_pesanan);



                    $barangData->stok = $barangData->stok + $pesananPembelidelete->jumlah_pembelian;
                    $barangData->save();
                    $stokbarangHistory = new StokBarangHistoryModel();
                    $stokbarangHistory->id_barang = $barangData->id_barang;
                    // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                    $stokbarangHistory->stok_masuk = $pesananPembelidelete->jumlah_pembelian;
                    $stokbarangHistory->stok_terkini = $barangData->stok;
                    $stokbarangHistory->save();


                    $pesananPembelidelete->delete();



                    // $stokBarangdelete = StokBarangModel::find($pesananPembelidelete->id_stokbarang);
                    // Simpan ke log
                    $logStokBarang = new LogStokBarangModel();
                    $logStokBarang->json_content = [
                        'type' => 'pembelian_update',
                        'data' => [
                            'no_nota' => $notaPembeli->no_nota,
                            'stok_keluar' => $pesanan['jumlah_pesanan'],
                            'pelanggan' => $pembeliData->id_pelanggan
                        ]
                    ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                    $logStokBarang->tipe_log = 'pesanan_update';
                    $logStokBarang->keterangan = 'Mengupdate hapus pesanan dari nota ' . $notaPembeli->no_nota  . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                    $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                    // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                    $logStokBarang->id_barang = $barangData->id_barang; // Sesuaikan dengan id_barang yang ada
                    $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    $logStokBarang->save();
                    // $stokBarangdelete->delete();
                }
            } elseif ($pesanan['type_pesanan'] == 'new') {
                // Membuat Pesanan Pembeli
                $pesananPembeli = new PesananPembeli;
                $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                $pesananPembeli->id_diskon = $diskonId;
                $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                $pesananPembeli->harga = $hargaSetelahDiskon;
                $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
                $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
                $pesananPembeli->diskon = $hargaDiskon;


                // $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                $stokTersedia = $barangData;
                if ($stokTersedia->stok >=  $pesanan['jumlah_pesanan']) {



                    // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                    // $barangData->save();
                    // $stokBarang = StokBarangModel::create([
                    //     'stok_keluar' => $pesanan['jumlah_pesanan'],
                    //     'id_barang' => $barangData->id_barang,
                    //     'tipe_stok' => 'pesanan'
                    // ]);
                    // Pindah ke PesananPembeli
                    // $pesananPembeli->id_stokbarang = $stokBarang->id;





                    $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                    $barangData->save();
                    $stokbarangHistory = new StokBarangHistoryModel();
                    $stokbarangHistory->id_barang = $barangData->id_barang;
                    // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                    $stokbarangHistory->stok_keluar = $pesanan['jumlah_pesanan'];
                    $stokbarangHistory->stok_terkini = $barangData->stok;
                    $stokbarangHistory->save();

                    // Simpan ke log
                    $logStokBarang = new LogStokBarangModel();
                    $logStokBarang->json_content = [
                        'type' => 'pembelian_update',
                        'data' => [
                            'no_nota' => $notaPembeli->no_nota,
                            'stok_keluar' => $pesanan['jumlah_pesanan'],
                            'pelanggan' => $pembeliData->id_pelanggan
                        ]
                    ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                    $logStokBarang->tipe_log = 'pesanan_update';
                    $logStokBarang->keterangan = 'Tambah barang di nota ' . $notaPembeli->no_nota  . ' pada pelanggan ' .  $pembeliData->nama_pembeli;
                    $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                    // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                    $logStokBarang->id_barang = $barangData->id_barang; // Sesuaikan dengan id_barang yang ada
                    $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    $logStokBarang->save();
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Terjadi Kesalahan');
                }
                $pesananPembeli->save();
            } else {
                return redirect()->back()->with('error', 'Unknown type_pesanan');
            }


            // Array data user dari request


            // Update data barang


        }



        // Menghitung lagi pesanan
        // Sub Total seluruhnya 
        $subTotal = 0;
        $totalDiskon = 0;
        $notaPembeliPesanan = NotaPembeli::with('PesananPembeli')->find($notaPembeli->id_nota);
        foreach ($notaPembeliPesanan->PesananPembeli as $pesananPembeli3) {


            $subTotal += $pesananPembeli3->harga *  $pesananPembeli3->jumlah_pembelian;
            $totalDiskon += $pesananPembeli3->diskon;
        }


        // Menghitung kembali total dari pesanan
        $updateNotaPembeli = NotaPembeli::with('bukuBesar')->find($notaPembeli->id_nota);



        $updateNotaPembeli->sub_total = $subTotal;
        $updateNotaPembeli->diskon = $request->get('diskon');
        $updateNotaPembeli->ongkir = $request->get('total_ongkir');

        // Perhitungan Pajak
        $nilaiTotal = $updateNotaPembeli->sub_total - $updateNotaPembeli->diskon;
        // $nilaiPajak = $nilaiTotal * ( $updateNotaPembeli->pajak / 100);
        $nilaiOngkir =  $updateNotaPembeli->ongkir;
        $updateNotaPembeli->total = $nilaiTotal + $nilaiOngkir;
        $updateNotaPembeli->save();






        // Perhitungan Kembali untuk laporan Piutang untuk hutang dan lunas
        if ($totalOld == $nominalTerbayarOld) {
            // Lunas ke lunas 
            $total_baru = $notaPembeliPesanan->total;
            $nominal_terbayar_baru = $notaPembeliPesanan->nominal_terbayar;
            if ($total_baru == $nominal_terbayar_baru) {
                // Update pada bukubesar
                // $RiwayatPiutangModel = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                // $bukuBesar = BukubesarModel::find($RiwayatPiutangModel->id_bukubesar);
                // $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                // $bukuBesar->save();
                $notaPembeliPesanan->nominal_terbayar = $notaPembeliPesanan->nominal_terbayar;
                // Periksa kondisi untuk tanggal penyelesaian
                
              

                $notaPembeliPesanan->save();

                $updateBukubesar = BukubesarModel::find($notaPembeliPesanan->id_bukubesar);
                $updateBukubesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $updateBukubesar->save();
            }

            // Lunas ke hutang
            else {


                // RiwayatHutangModel::where('id_barang', $barang->id_barang)->delete();
                $notaPembeliPesanan->nominal_terbayar = $notaPembeliPesanan->nominal_terbayar;



                // Periksa kondisi untuk tanggal penyelesaian
                // if (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->update) == $notaPembeliPesanan->total && is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
                //     $notaPembeliPesanan->tanggal_penyelesaian = $notaPembeliPesanan->updated_at;  // Atau $notaPembeliPesanan->updated_at jika diperlukan
                // } elseif (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->update) != $notaPembeliPesanan->total && !is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
                //     $notaPembeliPesanan->tanggal_penyelesaian = null;
                // }

                $notaPembeliPesanan->save();


                $updateBukubesar = BukubesarModel::find($notaPembeliPesanan->id_bukubesar);
                $updateBukubesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $updateBukubesar->save();



                $riwayatPiutang = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                foreach ($riwayatPiutang as $rpiutang) {
                    $rpiutang->delete();
                }


                // Update pada bukubesar
                // $RiwayatPiutangModel = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                // $bukuBesar = BukubesarModel::find($RiwayatPiutangModel->id_bukubesar);
                // $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                // $bukuBesar->save();


                // // Hapus seluruh bukubesar yang setelah edit
                // $RiwayatPiutangModelList = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                // $RiwayatPiutangModelList->skip(1)->each(function ($RiwayatPiutangModel) {
                //     $RiwayatPiutangModel->delete();
                // });
            }
        } else {

            $notaPembeliCheck = NotaPembeli::where('id_nota', $notaPembeliPesanan->id_nota)->first();
            $total_baru = $notaPembeliCheck->total;
            $nominal_terbayar_baru = $notaPembeliCheck->nominal_terbayar;

            // Hutang ke lunas
            if ($total_baru == $nominal_terbayar_baru) {

                // Update pada bukubesar
                // $RiwayatPiutangModel = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                // $bukuBesar = BukubesarModel::find($RiwayatPiutangModel->id_bukubesar);
                // $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                // $bukuBesar->save();


                // // Hapus seluruh bukubesar yang setelah edit
                // $RiwayatPiutangModelList = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                // $RiwayatPiutangModelList->skip(1)->each(function ($RiwayatPiutangModel) {
                //     $RiwayatPiutangModel->delete();
                // });


                $notaPembeliPesanan->nominal_terbayar = $notaPembeliPesanan->nominal_terbayar;
                // Periksa kondisi untuk tanggal penyelesaian
                // if (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->update) == $notaPembeliPesanan->total && is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
                //     $notaPembeliPesanan->tanggal_penyelesaian = $notaPembeliPesanan->updated_at;  // Atau $notaPembeliPesanan->updated_at jika diperlukan
                // } elseif (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->update) != $notaPembeliPesanan->total && !is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
                //     $notaPembeliPesanan->tanggal_penyelesaian = null;
                // }
                $notaPembeliPesanan->save();


                $updateBukubesar = BukubesarModel::find($notaPembeliPesanan->id_bukubesar);
                $updateBukubesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $updateBukubesar->save();

                $riwayatPiutang = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                foreach ($riwayatPiutang as $rpiutang) {
                    $rpiutang->delete();
                }

                // Hutang ke hutang
            } else if ($totalOld != $total_baru || $nominal_terbayar_baru !=  $nominalTerbayarOld) {

                $notaPembeliPesanan->nominal_terbayar = $notaPembeliPesanan->nominal_terbayar;
                // Periksa kondisi untuk tanggal penyelesaian
                // if (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->update) == $notaPembeliPesanan->total && is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
                //     $notaPembeliPesanan->tanggal_penyelesaian = $notaPembeliPesanan->updated_at;  // Atau $notaPembeliPesanan->updated_at jika diperlukan
                // } elseif (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->update) != $notaPembeliPesanan->total && !is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
                //     $notaPembeliPesanan->tanggal_penyelesaian = null;
                // }
                $notaPembeliPesanan->save();


                $updateBukubesar = BukubesarModel::find($notaPembeliPesanan->id_bukubesar);
                $updateBukubesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $updateBukubesar->save();

                $riwayatPiutang = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                foreach ($riwayatPiutang as $rpiutang) {
                    $rpiutang->delete();
                }
                // // Update pada bukubesar
                // $RiwayatPiutangModel = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                // $bukuBesar = BukubesarModel::find($RiwayatPiutangModel->id_bukubesar);
                // $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                // $bukuBesar->save();


                // // Hapus seluruh bukubesar yang setelah edit
                // $RiwayatPiutangModelList = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                // $RiwayatPiutangModelList->skip(1)->each(function ($RiwayatPiutangModel) {
                //     $RiwayatPiutangModel->delete();
                // });
            }
        }

        

        if (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->dp) == $notaPembeliPesanan->total && is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
            $notaPembeliPesanan->tanggal_penyelesaian = $notaPembeliPesanan->updated_at;  // Atau $notaPembeliPesanan->updated_at jika diperlukan
        } elseif (($notaPembeliPesanan->nominal_terbayar + $notaPembeliPesanan->dp) != $notaPembeliPesanan->total && !is_null($notaPembeliPesanan->tanggal_penyelesaian)) {
            $notaPembeliPesanan->tanggal_penyelesaian = null;
        }
        $notaPembeliPesanan->save();

        // Log Nota
        // Asumsikan $notaPembeli adalah instance dari model NotaPembeli yang sudah ada
        $notaPembeliToSave = NotaPembeli::with('PesananPembeli')->find($notaPembeliPesanan->id_nota)->toArray();
        $logNota = new LogNotaModel();
        $logNota->json_content = $notaPembeliToSave;
        $logNota->tipe_log = 'update';
        $logNota->keterangan = 'Update nota pembelian';
        $logNota->id_nota = $notaPembeli->id_nota;
        $logNota->id_admin = Auth::user()->id_admin; // Mengambil id_admin dari user yang sedang login
        $logNota->save();



        DB::commit();


        return redirect()->route('pemesanan.index')->with('success', 'Pesanan herhasil diupdate');
    }

    public function destroy($id)
    {

        // Temukan NotaPembeli dengan relasi yang terkait
        $notaPembeli = NotaPembeli::with([
            'bukuBesar',
            'Piutang',
            'Pembeli',
            // 'PesananPembeli',
            'PesananPembeli.stokBarang',
            'returPembelis',
            'returPembelis.returPesananPembelis',

        ])->where('id_nota', $id)->first();
        DB::beginTransaction();
        if ($notaPembeli) {
            // Hapus semua BukuBesar terkait
            // foreach ($notaPembeli->bukuBesar as $bukuBesar) {
            //     $bukuBesar->delete();
            // }
            $notaPembeli->bukuBesar->delete();
            foreach ($notaPembeli->Piutang as $Piutang) {
                $Piutang->delete();
            }

            // Hapus semua PesananPembeli dan StokBarang terkait
            foreach ($notaPembeli->PesananPembeli as $pesananPembeli2) {
                // Hapus StokBarang terkait dengan PesananPembeli2

                // dump($notaPembeli->PesananPembeli);
                // $stokBarangtoDelete = StokBarangModel::find($pesananPembeli2->id_stokbarang);




                $barang = Barang::find($pesananPembeli2->id_barang);

                $barang->stok += $pesananPembeli2->jumlah_pembelian;
                $barang->save();
                // Buat instance dari model
                $stokbarangHistory = new StokBarangHistoryModel();
                $stokbarangHistory->id_barang = $barang->id_barang;
                // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                $stokbarangHistory->stok_masuk = $pesananPembeli2->jumlah_pembelian;
                $stokbarangHistory->stok_terkini = $barang->stok;
                $stokbarangHistory->save();



                // Simpan ke log
                $logStokBarang = new LogStokBarangModel();
                $logStokBarang->json_content = [
                    'type' => 'pembeli_hapus',
                    'data' => [
                        'no_nota' => $notaPembeli->no_nota,
                        'stok_keluar' =>  $pesananPembeli2->jumlah_pembelian,
                        'pelanggan' => $notaPembeli->Pembeli->id_pembeli
                    ]
                ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                $logStokBarang->tipe_log = 'pesanan_delete';
                $logStokBarang->keterangan = 'Hapus pesanan barang dari ' . $notaPembeli->no_nota . ' pada pelanggan ' . $notaPembeli->Pembeli->nama_pembeli;
                $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                // $logStokBarang->id_stok_barang = $stokBarangtoDelete->id; // Sesuaikan dengan id_stok_barang yang ada
                $logStokBarang->id_barang = $pesananPembeli2->id_barang; // Sesuaikan dengan id_barang yang ada
                $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                $logStokBarang->save();
                // $stokBarangtoDelete->delete();


                // Hapus PesananPembeli2 itu sendiri
                $pesananPembeli2->delete();
            }


            // Hapus semua ReturPembeli dan ReturPesananPembeli terkait
            foreach ($notaPembeli->returPembelis as $returPembeli) {
                foreach ($returPembeli->returPesananPembelis as $returPesananPembeli) {

                    $returPesananPembeli->delete();
                }
                $returPembeli->delete();
            }

            // Hapus Semua Laporan Piutang
            // Check if NotaPembeli is found
            if ($notaPembeli) {
                // Delete all related bukuBesar records
                $notaPembeli->bukuBesar()->delete();
            }

            // Hapus NotaPembeli itu sendiri
            $notaPembeli->delete();

            DB::commit();

            return redirect()->route('pemesanan.index')->with('success', 'Nota Pembelian dihapus');
        } else {
            DB::rollBack();
            return redirect()->route('pemesanan.index')->with('error', 'Nota Pembelian gagal dihapus');
        }
    }
}
