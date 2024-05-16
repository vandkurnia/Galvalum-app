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
            // Buat data baru untuk PesananPembeli yang terhubung dengan NotaPembeli dan Barang
            $pesananPembeli = new PesananPembeli;
            $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
            $pesananPembeli->id_diskon = $diskonId;
            $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
            $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
            $pesananPembeli->harga = $hargaSetelahDiskon;
            $pesananPembeli->jenis_pembelian = $pesanan['jenis_pelanggan'];
            $pesananPembeli->harga_potongan = $pesanan['harga_potongan'];
            $pesananPembeli->diskon = $hargaDiskon;
            $pesananPembeli->save();
            // Array data user dari request


            // Update data barang

            $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
            if ($stokTersedia->stok >=  $pesanan['jumlah_pesanan']) {



                // $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                // $barangData->save();
                StokBarangModel::create([
                    'stok_keluar' => $pesanan['jumlah_pesanan'],
                    'id_barang' => $barangData->id_barang,
                ]);
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi Kesalahan');
            }
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
        $bukuBesarPembelian->sub_kategori = "lunas";
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
            'jenis_pembelian' => 'required|string',
            'status_pembelian' => 'required|string',
            'id_pembeli' => 'required|string'

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
        // $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        $notaPembeli->status_pembayaran = $request->get('status_pembelian'); // Contoh nilai untuk status_pembelian
        $notaPembeli->id_pembeli = $pembeliData->id_pembeli; // Contoh nilai untuk id_pembeli
        $notaPembeli->id_admin = Auth::id(); // Contoh nilai untuk id_admin

        $notaPembeli->save();

        // Penghapusan pesanan yang hilang
        $updateNotaPembeli = NotaPembeli::find($notaPembeli->id_nota);
        $updateNotaPembeli->pajak = $request->get('pajak');
        $updateNotaPembeli->total = ($updateNotaPembeli->sub_total  - $updateNotaPembeli->diskon) - $updateNotaPembeli->pajak;
        $updateNotaPembeli->save();


        DB::commit();


        return redirect()->route('pemesanan.index')->with('success', 'Pesanan herhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $notaPembeli = notaPembeli::with('bukuBesar')->where('id_nota', $id)->first();

        // $bukubesar = BukubesarModel::find($notaPembeli->id_bukubesar);

        if ($notaPembeli) {


            foreach ($notaPembeli->bukuBesar as $bukuBesar) {
                $bukuBesar->delete();
            }

            $notaPembeli->delete();

            return redirect()->route('pemesanan.index')->with('success', 'Nota Pembelian dihapus');
        } else {
            return redirect()->route('pemesanan.index')->with('error', 'Nota Pembelian gagal dihapus');
        }
    }
}
