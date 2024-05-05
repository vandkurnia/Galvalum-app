<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\DiskonModel;
use App\Models\Notabukubesar;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use App\Models\Retur\ReturPembeliModel;
use App\Models\Retur\ReturPesananPembeliModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturPembeliController extends Controller
{

    private function  hashToId($hash_id)
    {

        return ReturPembeliModel::where('hash_id_retur_pembeli', $hash_id)->first();
    }
    public function edit($id_retur)
    {

        $dataReturPembeli = ReturPembeliModel::find($this->hashToId($id_retur)->id_retur_pembeli);
        $dataPembeli = Pembeli::all();
        return view('retur.pembeli.edit', compact('dataReturPembeli', 'dataPembeli'));
    }
    public function add($id_nota)
    {
        $lastId = ReturPembeliModel::max('id_retur_pembeli');
        $lastId = $lastId ? $lastId : 0; // handle jika tabel kosong
        $lastId++;
        $noReturPembeli = 'RETUR' . date('Y') . date('mdHis') . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        $dataPembeli = Pembeli::all();


        $dataDiskon = DiskonModel::all();
        $dataPesanan = PesananPembeli::where('id_nota', $id_nota)->with('Barang', 'Barang.TipeBarang')->get();
        $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->with('Pembeli', 'PesananPembeli')->first();
        return view('retur.pembeli.add', compact('dataPembeli', 'noReturPembeli', 'dataDiskon', 'dataPesanan', 'notaPembelian'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'id_nota' => 'required|exists:nota_pembelis,id_nota',
            'tanggal_retur_pembeli' => 'required|date',
            'bukti_retur_pembeli' => 'required|file',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'status' => 'required|in:Belum Selesai,Selesai',
            'retur_murni' => 'required',
            'retur_tambahan' => 'required'
        ]);


        DB::beginTransaction();
        $notaPembelian = NotaPembeli::find($request->id_nota);
        $dataReturPembeli = new ReturPembeliModel();
        // $dataReturPembeli->hash_id_retur_pembeli = "KUcing Sigma";

        $totalIdReturPembeli = ReturPembeliModel::count();
        if ($totalIdReturPembeli === 0) {
            $totalIdReturPembeli = 1;
        } else {
            $totalIdReturPembeli = $totalIdReturPembeli + 1;
        }

        $dataReturPembeli->no_retur_pembeli = "RETUR" . date('YmdHis') . $totalIdReturPembeli;
        $dataReturPembeli->faktur_retur_pembeli = $notaPembelian->no_nota;
        $dataReturPembeli->tanggal_retur_pembeli = $request->tanggal_retur_pembeli;
        $dataReturPembeli->id_nota = $notaPembelian->id_nota;
        // Simpan file bukti_retur_pembeli
        if ($request->hasFile('bukti_retur_pembeli')) {
            $file = $request->file('bukti_retur_pembeli');
            $fileName = $file->getClientOriginalName();
            $file->storeAs('bukti_retur_pembeli', $fileName);
            $validatedData['bukti_retur_pembeli'] = $fileName;
        }
        $dataReturPembeli->bukti_retur_pembeli = $validatedData['bukti_retur_pembeli'];

        $dataReturPembeli->jenis_retur = $request->jenis_retur;
        $dataReturPembeli->total_nilai_retur = $request->total_nilai_retur;
        $dataReturPembeli->pengembalian_data = $request->pengembalian_data;
        $dataReturPembeli->kekurangan = $request->kekurangan;
        $dataReturPembeli->status = $request->status;
        $dataReturPembeli->id_pembeli = $notaPembelian->id_pembeli;



        $dataReturPembeli->total_nilai_retur = 0;
        $dataReturPembeli->pengembalian_data = 0;
        $dataReturPembeli->kekurangan = 0;


        $dataReturPembeli->save();



        // Membuat Retur Menu
        $returMurni = json_decode($request->get("retur_murni"), true);
        // dd($returMurni)
        foreach ($returMurni as $returMrni) {
            // Pesanan 
            $pesananData = PesananPembeli::where('id_pesanan', $returMrni['id_pesanan'])->first();
            $returPesanan = new ReturPesananPembeliModel();
            $returPesanan->id_retur_pembeli = $dataReturPembeli->id_retur_pembeli; // Contoh nilai id_retur_pembeli
            $returPesanan->id_pesanan_pembeli = $pesananData->id_pesanan; // Contoh nilai id_pesanan_pembeli
            $returPesanan->harga = $pesananData->harga; // Contoh nilai harga

            $returPesanan->qty = $returMrni['qty_retur']; // Contoh nilai qty
            $returPesanan->total =  ($pesananData->harga - $pesananData->diskon) *  $returPesanan->qty; // Contoh nilai total
            $returPesanan->save();
        }


        // Membuat Retur Tambahan
        $returTambahan = json_decode($request->get("retur_tambahan"), true);



        // Sub Total seluruhnya 
        $subTotal = 0;
        $totalDiskon = 0;
        foreach ($returTambahan as $returTmbhn) {
            // Data barang 
            $barangData = Barang::where('hash_id_barang', $returTmbhn['id_barang'])->lockForUpdate()->first();
            if (empty($barangData)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan barang, barang tidak ada');
            }
            $diskon = DiskonModel::where('hash_id_diskon', $returTmbhn['id_diskon'])->first();
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


            $hargaSetelahDiskon = $hargaSetelahDiskon - $returTmbhn['harga_potongan'];
            $subTotal += $hargaSetelahDiskon *  $returTmbhn['jumlah_pesanan'];
            $totalDiskon += $hargaDiskon;

            $pesananData = new PesananPembeli();
            $pesananData->jumlah_pembelian = $returTmbhn['jumlah_pesanan']; // Contoh nilai jumlah_pembelian
            $pesananData->harga =  $hargaSetelahDiskon; // Contoh nilai harga
            $pesananData->diskon = $hargaDiskon; // Contoh nilai diskon
            $pesananData->id_nota =  $notaPembelian->id_nota; // Contoh nilai id_nota
            $pesananData->id_barang = 1; // Contoh nilai id_barang
            $pesananData->jenis_pembelian = $returTmbhn['jenis_pelanggan']; // Contoh nilai jenis_pembelian
            $pesananData->harga_potongan = $returTmbhn['harga_potongan']; // Contoh nilai harga_potongan
            $pesananData->id_diskon =  $diskonId; // Contoh nilai id_diskon
            $pesananData->save();


            // Update data barang
            if ($barangData->stok >=  $returTmbhn['jumlah_pesanan']) {
                $barangData->stok = $barangData->stok - $returTmbhn['jumlah_pesanan'];
                $barangData->save();
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan data');
            }
        }




        // Perhitungan Nota Pembeli
        $updateNotaPembeli = NotaPembeli::find($notaPembelian->id_nota);

        $updateNotaPembeli->sub_total = $subTotal;
        $updateNotaPembeli->diskon = $request->get('diskon');
        $updateNotaPembeli->ongkir = $request->get('total_ongkir');



        // Perhitungan Pajak (Masih belum bisa)
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
            $bukubesar->debit =   $notaPembelian->nominal_terbayar; // Misalnya debit sebesar 1000
            $bukubesar->kredit = 0; // Kredit diisi 0 karena debit
            $bukubesar->save();



            $notaBukubesar = new Notabukubesar();
            $notaBukubesar->id_nota = $updateNotaPembeli->id_nota;
            $notaBukubesar->id_bukubesar = $bukubesar->id_bukubesar;
            $notaBukubesar->save();
        }





        DB::commit();
        dd("berhasil");

        return redirect()->route('retur.index')->with('success', 'Retur berhasil ditambahkan');
    }

    public function update(Request $request, $id_retur)
    {


        $request->validate([

            'no_retur_pembeli' => 'required',
            'faktur_retur_pembeli' => 'required',
            'tanggal_retur_pembeli' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'total_nilai_retur' => 'required|numeric',
            'pengembalian_data' => 'nullable|string',
            'kekurangan' => 'nullable|string',
            'status' => 'required|in:Belum Selesai,Selesai',
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
        ]);
        $returPembeli = new ReturPembeliModel;
        $returPembeli->update([

            'no_retur_pembeli' => $request->no_retur_pembeli,
            'faktur_retur_pembeli' => $request->faktur_retur_pembeli,
            'tanggal_retur_pembeli' => $request->tanggal_retur_pembeli,
            'jenis_retur' => $request->jenis_retur,
            'total_nilai_retur' => $request->total_nilai_retur,
            'pengembalian_data' => $request->pengembalian_data,
            'kekurangan' => $request->kekurangan,
            'status' => $request->status,
            'id_pembeli' => $request->id_pembeli,
        ]);

        if ($request->hasFile('bukti_retur_pembeli')) {
            $file = $request->file('bukti_retur_pembeli');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/retur_pembeli/', $fileName);
            $returPembeli->bukti_retur_pembeli = $fileName;
            $returPembeli->save();
        }



        return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui');
    }

    public function destroy($id_retur)
    {
        $dataReturPembeli = ReturPembeliModel::find($this->hashToId($id_retur)->id_retur_pembeli);
        $dataReturPembeli->delete();

        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
    }
}
