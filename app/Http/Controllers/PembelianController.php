<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\DiskonModel;
use App\Models\Notabukubesar;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use App\Models\StokBarangModel;
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


        // Nominal Terbayar
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

            $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
            if ($stokTersedia->stok >=  $pesanan['jumlah_pesanan']) {



                // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                // $barangData->save();
                $stokBarang = StokBarangModel::create([
                    'stok_keluar' => $pesanan['jumlah_pesanan'],
                    'id_barang' => $barangData->id_barang,
                ]);
                // Pindah ke PesananPembeli
                $pesananPembeli->id_stokbarang = $stokBarang->id;
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi Kesalahan');
            }
            $pesananPembeli->save();
        }





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

        // dump([
        //     'total' => $updateNotaPembeli
        // ]);
        // Membuat satu data baru
        $bukuBesarPembelian = new BukubesarModel();
        $bukuBesarPembelian->id_akunbayar = 1;
        $bukuBesarPembelian->tanggal = date('Y-m-d'); // Tanggal saat ini
        $bukuBesarPembelian->kategori = 'transaksi';
        $bukuBesarPembelian->keterangan = 'NOTA ' . $notaPembeli->no_nota; // Ganti dengan keterangan yang sesuai
        $bukuBesarPembelian->debit = $updateNotaPembeli->nominal_terbayar; // Misalnya debit sebesar 1000
        $bukuBesarPembelian->save();
        $updateNotaPembeli->bukuBesar()->save($bukuBesarPembelian);




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


            // $hargaSetelahDiskon = $hargaSetelahDiskon - $pesanan['harga_potongan'];
            // $subTotal += $hargaSetelahDiskon *  $pesanan['jumlah_pesanan'];
            // $totalDiskon += $hargaDiskon;






            // Data ada tetapi ada perubahan
            if ($pesanan['type_pesanan'] == 'exist') {
                // Pesanan Pembeli
                $pesananPembeli = PesananPembeli::where('id_nota', $notaPembeli->id_nota)->where('id_barang', $barangData->id_barang)->first();
                $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                $pesananPembeli->id_diskon = $diskonId;
                $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                $pesananPembeli->harga = $hargaSetelahDiskon;
                $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
                $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
                $pesananPembeli->diskon = $hargaDiskon;


                $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                if ($stokTersedia->stok >=  $pesanan['jumlah_pesanan']) {



                    // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                    // $barangData->save();
                    $stokBarang = StokBarangModel::find($pesananPembeli->id_stokbarang);

                    if ($stokBarang) {
                        $stokBarang->stok_keluar = $pesanan['jumlah_pesanan'];
                        $stokBarang->id_barang = $barangData->id_barang;
                        $stokBarang->save();
                    } else {
                        // Handle the case where the stock entry does not exist
                        // You might want to create a new entry or return an error
                        return redirect()->back()->with('error', 'Stock entry not found for this order.');
                    }
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Terjadi Kesalahan');
                }
                $pesananPembeli->save();


                // Cek Jika pesanan yang sudah ada mau dihapus
                if ($pesanan['terhapus'] == 'yes') {
                    // Hapus Pesanan dan Stok
                    $pesananPembelidelete = PesananPembeli::find($pesananPembeli->id_pesanan);
                    $pesananPembelidelete->delete();
                    $stokBarangdelete = StokBarangModel::find($pesananPembelidelete->id_stokbarang);
                    $stokBarangdelete->delete();
                }
            } elseif ($pesanan['type_pesanan'] == 'modified') {
                // Pesanan Pembeli
                $pesananPembeli = PesananPembeli::where('id_nota', $notaPembeli->id_nota)->where('id_barang', $barangData->id_barang)->first();
                $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                $pesananPembeli->id_diskon = $diskonId;
                $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                $pesananPembeli->harga = $hargaSetelahDiskon;
                $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
                $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
                $pesananPembeli->diskon = $hargaDiskon;
                $pesananPembeli->save();

                $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                if ($stokTersedia->stok >=  $pesanan['jumlah_pesanan']) {



                    // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                    // $barangData->save();
                    $stokBarang = StokBarangModel::find($pesananPembeli->id_stokbarang);

                    if ($stokBarang) {
                        $stokBarang->stok_keluar = $pesanan['jumlah_pesanan'];
                        $stokBarang->id_barang = $barangData->id_barang;
                        $stokBarang->save();
                    } else {
                        // Handle the case where the stock entry does not exist
                        // You might want to create a new entry or return an error
                        return redirect()->back()->with('error', 'Stock entry not found for this order.');
                    }
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Terjadi Kesalahan');
                }



                // Cek Jika pesanan yang sudah ada mau dihapus
                if ($pesanan['terhapus'] == 'yes') {
                    // Hapus Pesanan dan Stok
                    $pesananPembelidelete = PesananPembeli::find($pesananPembeli->id_pesanan);
                    $pesananPembelidelete->delete();
                    $stokBarangdelete = StokBarangModel::find($pesananPembelidelete->id_stokbarang);
                    $stokBarangdelete->delete();
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


                $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                if ($stokTersedia->stok >=  $pesanan['jumlah_pesanan']) {



                    // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                    // $barangData->save();
                    $stokBarang = StokBarangModel::create([
                        'stok_keluar' => $pesanan['jumlah_pesanan'],
                        'id_barang' => $barangData->id_barang,
                    ]);
                    // Pindah ke PesananPembeli
                    $pesananPembeli->id_stokbarang = $stokBarang->id;
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




        DB::commit();


        return redirect()->route('pemesanan.index')->with('success', 'Pesanan herhasil diupdate');
    }

    public function destroy($id)
    {

        // Temukan NotaPembeli dengan relasi yang terkait
        $notaPembeli = NotaPembeli::with([
            'bukuBesar',
            // 'PesananPembeli',
            'PesananPembeli.stokBarang',
            'returPembelis',
            'returPembelis.returPesananPembelis'
        ])->where('id_nota', $id)->first();
        DB::beginTransaction();
        if ($notaPembeli) {
            // Hapus semua BukuBesar terkait
            foreach ($notaPembeli->bukuBesar as $bukuBesar) {
                $bukuBesar->delete();
            }

            // Hapus semua PesananPembeli dan StokBarang terkait
            foreach ($notaPembeli->PesananPembeli as $pesananPembeli2) {
                // Hapus StokBarang terkait dengan PesananPembeli2
                $pesananPembeli2->stokBarang->delete();
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
