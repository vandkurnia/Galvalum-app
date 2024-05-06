<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\DiskonModel;
use App\Models\Notabukubesar;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
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
            'tenggat_bayar' => 'required',
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
                'jenis_pembeli' => $request->jenis_pelanggan

            ] // Isi dengan data default jika pembeli baru dibuat
        );

        $notaPembeli = new NotaPembeli;
        $notaPembeli->no_nota = $request->get('no_nota');
        // $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        $notaPembeli->status_pembayaran = $request->get('status_pembelian'); // Contoh nilai untuk status_pembelian
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
            $barangData = Barang::where('hash_id_barang', $pesanan['id_barang'])->lockForUpdate()->first();
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
            if ($barangData->stok >=  $pesanan['jumlah_pesanan']) {
                $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                $barangData->save();
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan data');
            }
        }





        $updateNotaPembeli = NotaPembeli::find($notaPembeli->id_nota);

        $updateNotaPembeli->sub_total = $subTotal;
        $updateNotaPembeli->diskon = $request->get('diskon');
        $updateNotaPembeli->ongkir = $request->get('total_ongkir');

        // Perhitungan Pajak
        $nilaiTotal = $updateNotaPembeli->sub_total - $updateNotaPembeli->diskon;
        // $nilaiPajak = $nilaiTotal * ( $updateNotaPembeli->pajak / 100);
        // $updateNotaPembeli->total = $nilaiTotal + $nilaiPajak;
        $updateNotaPembeli->total = $nilaiTotal + $updateNotaPembeli->ongkir;
        $updateNotaPembeli->nominal_terbayar =  $updateNotaPembeli->total;

        if ($updateNotaPembeli->status_pembayaran == 'lunas') {
            $updateNotaPembeli->nominal_terbayar =  $updateNotaPembeli->total;
            $updateNotaPembeli->tenggat_bayar = $request->get('tenggat_bayar');
        } else {
            $updateNotaPembeli->nominal_terbayar =  $request->get('nominal_terbayar');
            $updateNotaPembeli->tenggat_bayar = $request->get('tenggat_bayar');
        }
        $updateNotaPembeli->tenggat_bayar = $request->get('tenggat_bayar');
        $updateNotaPembeli->save();


        // Mendapatkan tanggal hari ini
        $tanggal = Carbon::now();
        // Data yang akan diinput
        if ($updateNotaPembeli->status_pembayaran == 'lunas') {

            // Membuat satu data baru
            $bukubesar = new BukubesarModel();
            $bukubesar->hash_id_bukubesar = 'hash_id_bukubesar_1';
            $bukubesar->id_akunbayar = 1;
            $bukubesar->tanggal = date('Y-m-d'); // Tanggal saat ini
            $bukubesar->kategori = 'transaksi';
            $bukubesar->sub_kategori = "lunas";
            $bukubesar->keterangan = 'NOTA ' . $updateNotaPembeli->no_nota . ' LUNAS'; // Ganti dengan keterangan yang sesuai
            $bukubesar->debit = $updateNotaPembeli->total; // Misalnya debit sebesar 1000
            $bukubesar->kredit = 0; // Kredit diisi 0 karena debit
            $bukubesar->save();

            $notaBukubesar = new Notabukubesar();
            $notaBukubesar->id_nota = $updateNotaPembeli->id_nota;
            $notaBukubesar->id_bukubesar = $bukubesar->id_bukubesar;
            $notaBukubesar->save();
        } else {

            if ($updateNotaPembeli->nominal_terbayar >  $updateNotaPembeli->total) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Nominal terbayar lebih besar dari total pembelian');
            }
            // Membuat satu data baru
            $bukubesar = new BukubesarModel();
            $bukubesar->id_akunbayar = 1;
            $bukubesar->tanggal = date('Y-m-d'); // Tanggal saat ini
            $bukubesar->kategori = 'transaksi';
            $bukubesar->sub_kategori = "piutang";
            $bukubesar->keterangan = 'NOTA ' . $updateNotaPembeli->no_nota . ' PIUTANG'; // Ganti dengan keterangan yang sesuai
            $bukubesar->debit =   $notaPembeli->nominal_terbayar; // Misalnya debit sebesar 1000
            $bukubesar->kredit = 0; // Kredit diisi 0 karena debit
            $bukubesar->save();



            $notaBukubesar = new Notabukubesar();
            $notaBukubesar->id_nota = $updateNotaPembeli->id_nota;
            $notaBukubesar->id_bukubesar = $bukubesar->id_bukubesar;
            $notaBukubesar->save();
        }





        DB::commit();


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
        $notaPembeli = notaPembeli::where('id_nota', $id)->first();
        $bukubesar = BukubesarModel::find($notaPembeli->id_bukubesar);

        if ($notaPembeli) {
            $bukubesar->delete();
            $notaPembeli->delete();

            return redirect()->route('pemesanan.index')->with('success', 'Nota Pembelian dihapus');
        } else {
            return redirect()->route('pemesanan.index')->with('error', 'Nota Pembelian gagal dihapus');
        }
    }
}
