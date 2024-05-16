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
use App\Models\StokBarangModel;
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
            'bukti_retur_pembeli' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            // 'status' => 'required|in:Belum Selesai,Selesai',
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
        // Decode data JSON menjadi array asosiatif
        $fileData = json_decode($request->bukti_retur_pembeli, true);

        // Mendapatkan base64 data dari JSON
        $base64Data = $fileData['data'];

        // Decode base64 data ke file binary
        $fileBinary = base64_decode($base64Data);

        // Direktori tujuan penyimpanan file
        $targetDirectory = public_path('retur/pembeli/');

        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true); // Buat direktori secara rekursif
        }

        // Tambahkan .gitignore untuk menghindari terkirimnya gambar
        $gitignoreFile = $targetDirectory . '.gitignore';
        if (!file_exists($gitignoreFile)) {
            file_put_contents($gitignoreFile, "*\n!.gitignore"); // Isi .gitignore dengan aturan umum
        }

        // Membuat nama file yang unik (misalnya, gabungan dari id dan nama file)
        $fileName = $fileData['id'] . '_' . date('Ymdhis') . '_' . $fileData['name'];

        // Menyimpan file ke direktori tujuan
        file_put_contents($targetDirectory . $fileName, $fileBinary);
        $dataReturPembeli->bukti_retur_pembeli = $fileName;

        $dataReturPembeli->jenis_retur = $request->jenis_retur;
        $dataReturPembeli->total_nilai_retur = $request->total_nilai_retur;
        $dataReturPembeli->pengembalian_data = $request->pengembalian_data;
        $dataReturPembeli->kekurangan = $request->kekurangan;
        // $dataReturPembeli->status = $request->status;
        $dataReturPembeli->status = 'selesai';
        $dataReturPembeli->id_pembeli = $notaPembelian->id_pembeli;



        $dataReturPembeli->total_nilai_retur = 0;
        $dataReturPembeli->pengembalian_data = 0;
        $dataReturPembeli->kekurangan = 0;


        $dataReturPembeli->save();



        // Membuat Retur Menu
        $returMurni = json_decode($request->get("retur_murni"), true);
        // dd($returMurni)

        $subTotalReturMurni = 0;

        foreach ($returMurni as $returMrni) {
            // Memasukkan ke ReturPesanan 
            $pesananData = PesananPembeli::where('id_pesanan', $returMrni['id_pesanan'])->first();


            $returPesanan = new ReturPesananPembeliModel();
            $returPesanan->id_retur_pembeli = $dataReturPembeli->id_retur_pembeli;
            $returPesanan->id_pesanan_pembeli = $pesananData->id_pesanan;
            $returPesanan->harga = $pesananData->harga;
            $returPesanan->qty_sebelum_perubahan =$pesananData->jumlah_pembelian;
            $returPesanan->qty = $returMrni['qty_retur'];
            $returPesanan->type_retur_pesanan = "retur_murni";
            $returPesanan->total = ($pesananData->harga - $pesananData->diskon) * $returPesanan->qty;
            $returPesanan->save();



            // Mengupdate Jumlah Pembelian
            $pesananData->jumlah_pembelian = $pesananData->jumlah_pembelian - $returPesanan->qty;
            $pesananData->save();
            // Check if the returned item is damaged or not
            if ($dataReturPembeli->jenis_retur == 'Tidak Rusak') {
                // Update the stock if the item is not damaged
                $stokBarang = new StokBarangModel();
                $stokBarang->stok_masuk += $returPesanan->qty;
                $stokBarang->id_barang = $pesananData->id_barang;
                $stokBarang->save();

                // Associate the return order with the stock entry
                $returPesanan->id_stok_barang = $stokBarang->id_stok_barang;
                $returPesanan->save();
            } else {
                // Handle damaged item case if needed
                // No need to update stock for damaged items
            }







            // Lalu cek apakah stok pesanan sama dengan 0, jika iya maka hapus saja tetapi jika nggak 0 maka tidak apa - apa
            $pesananCekQtynya = PesananPembeli::where('id_pesanan', $returMrni['id_pesanan'])->first();
            if ($pesananCekQtynya->jumlah_pembelian == 0) {
                $pesananCekQtynya->delete();
            }

            // Menghitung subTotalReturMurni
            $barangData = Barang::with('stokBarang')->where('id_barang', $pesananData->id_barang)->first();
            $hargaSetelahDiskon = $barangData->harga_barang - $pesananData->diskon;
            $hargaSetelahDiskon = $hargaSetelahDiskon - $pesananData->harga_potongan;
            $subTotalReturMurni += $hargaSetelahDiskon *  $pesananData->jumlah_pembelian;



            // Memasukkan ke stok barang berdasarkan rusak atau tidak

        }


        // Membuat Retur Tambahan
        $returTambahan = json_decode($request->get("retur_tambahan"), true);



        // Sub Total seluruhnya 
        $subTotalbaru = 0;
        $totalDiskon = 0;
        foreach ($returTambahan as $returTmbhn) {
            // Data barang 
            $barangData = Barang::where('hash_id_barang', $returTmbhn['id_barang'])->first();
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
            $subTotalbaru += $hargaSetelahDiskon *  $returTmbhn['jumlah_pesanan'];
            $totalDiskon += $hargaDiskon;
            $pesananData = PesananPembeli::where('id_nota', $notaPembelian->id_nota)
                ->where('id_barang', $barangData->id_barang)
                ->withTrashed()->first();

            if ($pesananData) {


                $pesananSebelumnya = $pesananData->jumlah_pembelian;
                // Pesanan sudah ada, tambahkan jumlah pembelian
                $pesananData->jumlah_pembelian += $returTmbhn['jumlah_pesanan'];
                $pesananData->harga = $hargaSetelahDiskon; // Contoh nilai harga
                $pesananData->diskon = $hargaDiskon; // Contoh nilai diskon
                $pesananData->jenis_pembelian = $returTmbhn['jenis_pelanggan']; // Contoh nilai jenis_pembelian
                $pesananData->harga_potongan = $returTmbhn['harga_potongan']; // Contoh nilai harga_potongan
                $pesananData->id_diskon = $diskonId; // Contoh nilai id_diskon
                $pesananData->save();

                // Jika Deleted maka ubah ke null
                // Belum


                // Simpan Retur Tambah Stok
                $returPesanan2 = new ReturPesananPembeliModel();
                $returPesanan2->id_retur_pembeli = $dataReturPembeli->id_retur_pembeli;
                $returPesanan2->id_pesanan_pembeli = $pesananData->id_pesanan;
                $returPesanan2->harga = $pesananData->harga;
                $returPesanan2->qty =  $pesananData->jumlah_pembelian;
                $returPesanan2->type_retur_pesanan = 'retur_tambah_stok';
                $returPesanan2->qty_sebelum_perubahan = $pesananSebelumnya;
                $returPesanan2->total = ($pesananData->harga - $pesananData->diskon) * $returPesanan2->qty;
                $returPesanan2->save();
            } else {
                // Pesanan belum ada, buat pesanan baru
                $pesananData = PesananPembeli::create([
                    'jumlah_pembelian' => $returTmbhn['jumlah_pesanan'], // Contoh nilai jumlah_pembelian
                    'harga' => $hargaSetelahDiskon, // Contoh nilai harga
                    'diskon' => $hargaDiskon, // Contoh nilai diskon
                    'id_nota' => $notaPembelian->id_nota, // Contoh nilai id_nota
                    'id_barang' => $barangData->id_barang, // Contoh nilai id_barang
                    'jenis_pembelian' => $returTmbhn['jenis_pelanggan'], // Contoh nilai jenis_pembelian
                    'harga_potongan' => $returTmbhn['harga_potongan'], // Contoh nilai harga_potongan
                    'id_diskon' => $diskonId, // Contoh nilai id_diskon
                ]);



                //   Simpan Retur
                $returPesanan2 = new ReturPesananPembeliModel();
                $returPesanan2->id_retur_pembeli = $dataReturPembeli->id_retur_pembeli;
                $returPesanan2->id_pesanan_pembeli = $pesananData->id_pesanan;
                $returPesanan2->harga = $pesananData->harga;
                $returPesanan2->qty =  $pesananData->jumlah_pembelian;
                $returPesanan2->qty_sebelum_perubahan = 0;
                $returPesanan2->type_retur_pesanan = 'retur_tambah_barang';
                $returPesanan2->total = ($pesananData->harga - $pesananData->diskon) * $returPesanan2->qty;
                $returPesanan2->save();
            }


            // Update data barang
            $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
            if ($stokTersedia->stok >=  $returTmbhn['jumlah_pesanan']) {
                $stokBarang = StokBarangModel::create([
                    'stok_keluar' => $returTmbhn['jumlah_pesanan'],
                    'id_barang' => $barangData->id_barang,
                ]);
                $returPesanan2->id_stok_barang = $stokBarang->id_stok_barang;
                $returPesanan2->save();
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan data');
            }
        }




        // Perhitungan Nota Pembeli
        $updateNotaPembeli = NotaPembeli::find($notaPembelian->id_nota);

        $updateNotaPembeli->sub_total = $subTotalbaru + $subTotalReturMurni;

        // $updateNotaPembeli->diskon = $request->get('diskon');
        // $updateNotaPembeli->ongkir = $request->get('ongkir');



        // Perhitungan Pajak (Masih belum bisa)
        $nilaiTotal = $updateNotaPembeli->sub_total - $updateNotaPembeli->diskon;
        // $nilaiPajak = $nilaiTotal * ( $updateNotaPembeli->pajak / 100);
        // $updateNotaPembeli->total = $nilaiTotal + $nilaiPajak;
        $updateNotaPembeli->total = $nilaiTotal + $updateNotaPembeli->ongkir;
        $updateNotaPembeli->save();
        DB::commit();

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
