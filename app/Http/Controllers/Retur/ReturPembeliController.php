<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\DiskonModel;
use App\Models\Log\LogNotaModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\Notabukubesar;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use App\Models\Retur\ReturPembeliModel;
use App\Models\Retur\ReturPesananPembeliModel;
use App\Models\StokBarangHistoryModel;
use App\Models\StokBarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturPembeliController extends Controller
{

    private function  hashToId($hash_id)
    {

        return ReturPembeliModel::where('hash_id_retur_pembeli', $hash_id)->first();
    }
    public function edit($id_retur)
    {

        // Kalau jadi maka return
        abort(404, 'Tampilan Halaman tidak berizin :)');

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
            'retur_tambahan' => 'required',
            'total_nilai_retur' => 'required|numeric|min:0',
            'nominal_terbayar' => 'required'
        ]);


        DB::beginTransaction();
        $notaPembelian = NotaPembeli::with('Pembeli')->find($request->id_nota);
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



        // $dataReturPembeli->total_nilai_retur = 0;
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

            if (!$pesananData) {
                // Pesanan tidak ditemukan atau tidak valid
                return redirect()->back()->with(['error' => 'Pesanan tidak valid']);
            }


            $returPesanan = new ReturPesananPembeliModel();
            $returPesanan->id_retur_pembeli = $dataReturPembeli->id_retur_pembeli;
            $returPesanan->id_pesanan_pembeli = $pesananData->id_pesanan;
            $returPesanan->harga = $pesananData->harga;
            $returPesanan->qty_sebelum_perubahan = $pesananData->jumlah_pembelian;
            $returPesanan->qty = $returMrni['qty_retur'];
            $returPesanan->type_retur_pesanan = "retur_murni_rusak";
            $returPesanan->total = ($pesananData->harga - $pesananData->diskon) * $returPesanan->qty;
            $returPesanan->save();



            // Mengupdate Jumlah Pembelian
            $pesananData->jumlah_pembelian = $pesananData->jumlah_pembelian - $returPesanan->qty;
            $pesananData->save();
            // Check if the returned item is damaged or not
            switch ($dataReturPembeli->jenis_retur) {
                case 'Tidak Rusak':
                    // Update the stock if the item is not damaged
                    // $stokBarang = StokBarangModel::find($pesananData->id_stokbarang);
                    // $stokBarang->stok_keluar =  $pesananData->jumlah_pembelian;
                    // $stokBarang->id_barang = $pesananData->id_barang;
                    // $stokBarang->save();




                    // $perbedaan = $pesananData->jumlah_pembelian - $returPesanan->qty;
                    $barang = Barang::find($pesananData->id_barang);

                    $stokLama =  $barang->stok;
                    $stokBaru = 0;
                    $barang->stok = $barang->stok +  $returPesanan->qty;
                    $stokBaru = $barang->stok;
                    $barang->save();
                    if ($stokLama != $stokBaru) {
                        // Buat instance dari model
                        $stokbarangHistory = new StokBarangHistoryModel();
                        $stokbarangHistory->id_barang = $barang->id_barang;
                        $stokbarangHistory->stok_masuk = $returPesanan->qty;
                        // $stokbarangHistory->stok_keluar = $item['qty'];

                        $stokbarangHistory->stok_terkini = $barang->stok;
                        $stokbarangHistory->save();

                        // Simpan ke log
                        $logStokBarang = new LogStokBarangModel();
                        $logStokBarang->json_content = [
                            'type' => 'retur_pembeli_store',
                            'data' => [
                                'pelanggan' => $notaPembelian->id_pelanggan,
                                'no_nota' => $notaPembelian->no_nota,

                            ]
                        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                        $logStokBarang->tipe_log = 'retur_pembeli_create';
                        $logStokBarang->keterangan = 'Pengembalian barang tidak rusak sebanyak ' . $stokbarangHistory->stok_masuk . ' pada pelanggan ' . $notaPembelian->Pembeli->nama_pembeli;
                        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
                        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                        $logStokBarang->save();
                    }
                    // Associate the return order with the stock entry
                    $returPesanan = ReturPesananPembeliModel::find($returPesanan->id_retur_pesanan);
                    $returPesanan->type_retur_pesanan = "retur_murni_tidak_rusak";
                    $returPesanan->save();

                    break;
                default:
                    # code...
                    break;
            }







            // Lalu cek apakah stok pesanan sama dengan 0, jika iya maka hapus saja tetapi jika nggak 0 maka tidak apa - apa
            $pesananCekQtynya = PesananPembeli::where('id_pesanan', $returMrni['id_pesanan'])->first();
            if ($pesananCekQtynya->jumlah_pembelian == 0) {

                // $stokBarangDelete = StokBarangModel::find($pesananCekQtynya->id_stokbarang);





                // $perbedaan = $pesananData->jumlah_pembelian - $returPesanan->qty;
                $barang = Barang::find($pesananData->id_barang);
                $barang->stok = $barang->stok +  $returPesanan->qty;
                $barang->save();
                // Buat instance dari model
                $stokbarangHistory = new StokBarangHistoryModel();
                $stokbarangHistory->id_barang = $barang->id_barang;
                //   $stokbarangHistory->stok_masuk =$returPesanan->qty;
                // $stokbarangHistory->stok_keluar = $item['qty'];

                $stokbarangHistory->stok_terkini = $barang->stok;
                $stokbarangHistory->save();
                // Simpan ke log
                $logStokBarang = new LogStokBarangModel();
                $logStokBarang->json_content = [
                    'type' => 'retur_pembeli_store',
                    'data' => [
                        'pelanggan' => $notaPembelian->id_pelanggan,
                        'no_nota' => $notaPembelian->no_nota,

                    ]
                ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                $logStokBarang->tipe_log = 'retur_pembeli_create';
                $logStokBarang->keterangan = 'Pesanan dihapus di nota ' . $notaPembelian->no_nota . ' pada pelanggan ' . $notaPembelian->Pembeli->nama_pembeli;
                $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
                $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                $logStokBarang->save();
                // $stokBarangDelete->delete();
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


            // Diskon diskon
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
            $pesananData = PesananPembeli::with('returPesananPembeli')->where('id_nota', $notaPembelian->id_nota)
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
                $returPesanan2->qty = $returTmbhn['jumlah_pesanan'];
                $returPesanan2->type_retur_pesanan = 'retur_tambah_stok';
                $returPesanan2->qty_sebelum_perubahan = $pesananSebelumnya;
                $returPesanan2->total = ($pesananData->harga - $pesananData->diskon) * $returPesanan2->qty;
                $returPesanan2->save();


                // Update data barang
                // $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                $stokTersedia = $barangData;
                if ($stokTersedia->stok >=  $returTmbhn['jumlah_pesanan']) {

                    // Cari StokBarang termasuk yang sudah dihapus
                    // $stokBarang = StokBarangModel::withTrashed()->find($pesananData->id_stokbarang);

                    // if ($stokBarang) {


                    //     $returMurniRusak = ReturPesananPembeliModel::where('id_pesanan_pembeli', $pesananData->id_pesanan)
                    //         ->where('id_retur_pembeli', $dataReturPembeli->id_retur_pembeli)
                    //         ->where('type_retur_pesanan', 'retur_murni_rusak')
                    //         ->first();

                    //     $returMurniTidakRusak = ReturPesananPembeliModel::where('id_pesanan_pembeli', $pesananData->id_pesanan)
                    //         ->where('id_retur_pembeli', $dataReturPembeli->id_retur_pembeli)
                    //         ->where('type_retur_pesanan', 'retur_murni_tidak_rusak')
                    //         ->first();

                    //     if ($returMurniRusak) {
                    //         $stokBarang->stok_keluar = $returMurniRusak->qty + $pesananData->jumlah_pembelian;
                    //         $stokBarang->id_barang = $barangData->id_barang;
                    //         $stokBarang->save();

                    //         $barang = Barang::find($pesananData->id_barang);
                    //         $barang->stok = $barang->stok - $returTmbhn['jumlah_pesanan'];
                    //         $barang->save();

                    //         // Create an instance of the model
                    //         $stokbarangHistory = new StokBarangHistoryModel();
                    //         $stokbarangHistory->id_barang = $barang->id_barang;
                    //         $stokbarangHistory->stok_keluar = $returTmbhn['jumlah_pesanan'];
                    //         $stokbarangHistory->stok_terkini = $barang->stok;
                    //         $stokbarangHistory->save();

                    //         // Save to log
                    //         $logStokBarang = new LogStokBarangModel();
                    //         $logStokBarang->json_content = json_encode($stokBarang); // Ensure json_content is a valid JSON string
                    //         $logStokBarang->tipe_log = 'retur_pembeli_create';
                    //         $logStokBarang->keterangan = 'Retur Tambah Stok';
                    //         $logStokBarang->id_admin = Auth::user()->id_admin;
                    //         $logStokBarang->id_stok_barang = $stokBarang->id;
                    //         $logStokBarang->id_barang = $stokBarang->id_barang;
                    //         $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    //         $logStokBarang->save();
                    //     }

                    //     if ($returMurniTidakRusak) {
                    //         $stokBarang->stok_keluar = $pesananData->jumlah_pembelian;
                    //         $stokBarang->id_barang = $barangData->id_barang;
                    //         $stokBarang->save();

                    //         $barang = Barang::find($pesananData->id_barang);
                    //         $barang->stok -= $returTmbhn['jumlah_pesanan'];
                    //         $barang->save();

                    //         // Create an instance of the model
                    //         $stokbarangHistory = new StokBarangHistoryModel();
                    //         $stokbarangHistory->id_barang = $barang->id_barang;
                    //         $stokbarangHistory->stok_keluar = $returTmbhn['jumlah_pesanan'];
                    //         $stokbarangHistory->stok_terkini = $barang->stok;
                    //         $stokbarangHistory->save();

                    //         // Save to log
                    //         $logStokBarang = new LogStokBarangModel();
                    //         $logStokBarang->json_content = json_encode($stokBarang); // Ensure json_content is a valid JSON string
                    //         $logStokBarang->tipe_log = 'retur_pembeli_create';
                    //         $logStokBarang->keterangan = 'Retur Tambah Stok';
                    //         $logStokBarang->id_admin = Auth::user()->id_admin;
                    //         $logStokBarang->id_stok_barang = $stokBarang->id;
                    //         $logStokBarang->id_barang = $stokBarang->id_barang;
                    //         $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    //         $logStokBarang->save();
                    //     }



                    //     // Restore jika stok_keluar > 0
                    //     if ($stokBarang->stok_keluar > 0 && $stokBarang->trashed()) {
                    //         $stokBarang->restore();
                    //     }
                    // }

                    $returMurniRusak = ReturPesananPembeliModel::where('id_pesanan_pembeli', $pesananData->id_pesanan)
                        ->where('id_retur_pembeli', $dataReturPembeli->id_retur_pembeli)
                        ->where('type_retur_pesanan', 'retur_murni_rusak')
                        ->first();

                    $returMurniTidakRusak = ReturPesananPembeliModel::where('id_pesanan_pembeli', $pesananData->id_pesanan)
                        ->where('id_retur_pembeli', $dataReturPembeli->id_retur_pembeli)
                        ->where('type_retur_pesanan', 'retur_murni_tidak_rusak')
                        ->first();

                    if ($returMurniRusak) {
                        // $stokBarang->stok_keluar = $returMurniRusak->qty + $pesananData->jumlah_pembelian;
                        // $stokBarang->id_barang = $barangData->id_barang;
                        // $stokBarang->save();

                        $barang = Barang::find($pesananData->id_barang);
                        $barang->stok = $barang->stok - $returTmbhn['jumlah_pesanan'];
                        $barang->save();

                        // Create an instance of the model
                        $stokbarangHistory = new StokBarangHistoryModel();
                        $stokbarangHistory->id_barang = $barang->id_barang;
                        $stokbarangHistory->stok_keluar = $returTmbhn['jumlah_pesanan'];
                        $stokbarangHistory->stok_terkini = $barang->stok;
                        $stokbarangHistory->save();

                        // Save to log
                        $logStokBarang = new LogStokBarangModel();
                        $logStokBarang->json_content = [
                            'type' => 'retur_pembeli_store',
                            'data' => [
                                'pelanggan' => $notaPembelian->id_pelanggan,
                                'no_nota' => $notaPembelian->no_nota,

                            ]
                        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                        $logStokBarang->tipe_log = 'retur_pembeli_create';
                        $logStokBarang->keterangan = 'Retur tambah pesanan  ' . $notaPembelian->no_nota . ' pada pelanggan ' . $notaPembelian->Pembeli->nama_pembeli;
                        $logStokBarang->id_admin = Auth::user()->id_admin;
                        // $logStokBarang->id_stok_barang = $stokBarang->id;
                        $logStokBarang->id_barang = $barang->id_barang;
                        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                        $logStokBarang->save();
                    }

                    if ($returMurniTidakRusak) {
                        // $stokBarang->stok_keluar = $pesananData->jumlah_pembelian;
                        // $stokBarang->id_barang = $barangData->id_barang;
                        // $stokBarang->save();

                        $barang = Barang::find($pesananData->id_barang);
                        $barang->stok -= $returTmbhn['jumlah_pesanan'];
                        $barang->save();

                        // Create an instance of the model
                        $stokbarangHistory = new StokBarangHistoryModel();
                        $stokbarangHistory->id_barang = $barang->id_barang;
                        $stokbarangHistory->stok_keluar = $returTmbhn['jumlah_pesanan'];
                        $stokbarangHistory->stok_terkini = $barang->stok;
                        $stokbarangHistory->save();

                        // Save to log
                        $logStokBarang = new LogStokBarangModel();
                        $logStokBarang->json_content = [
                            'type' => 'retur_pembeli_store',
                            'data' => [
                                'pelanggan' => $notaPembelian->id_pelanggan,
                                'no_nota' => $notaPembelian->no_nota,

                            ]
                        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                        $logStokBarang->tipe_log = 'retur_pembeli_create';
                        $logStokBarang->keterangan = 'Retur tambah pesanan  ' . $notaPembelian->no_nota . ' pada pelanggan ' . $notaPembelian->Pembeli->nama_pembeli;

                        $logStokBarang->id_admin = Auth::user()->id_admin;
                        // $logStokBarang->id_stok_barang = $stokBarang->id;
                        $logStokBarang->id_barang = $barang->id_barang;
                        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                        $logStokBarang->save();
                    }
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Gagal memasukkan data');
                }


                // Cek pesanan terbaru
                $pesanan = PesananPembeli::withTrashed()->find($pesananData->id_pesanan);
                // Check if the record exists
                if ($pesanan) {
                    $jumlah_pembelian_terbaru = $pesanan->jumlah_pembelian;

                    // If jumlah_pembelian is greater than 0, restore the record
                    if ($jumlah_pembelian_terbaru > 0) {
                        $pesanan->restore();
                    }
                    // If jumlah_pembelian is 0 or less, force delete the record
                    else {
                        $pesanan->delete();
                    }
                }
            } else {
                // Pesanan belum ada, buat pesanan baru
                $pesananData = new PesananPembeli();
                $pesananData->jumlah_pembelian = $returTmbhn['jumlah_pesanan']; // Contoh nilai jumlah_pembelian
                $pesananData->harga = $hargaSetelahDiskon; // Contoh nilai harga
                $pesananData->diskon = $hargaDiskon; // Contoh nilai diskon
                $pesananData->id_nota = $notaPembelian->id_nota; // Contoh nilai id_nota
                $pesananData->id_barang = $barangData->id_barang; // Contoh nilai id_barang
                $pesananData->jenis_pembelian = $returTmbhn['jenis_pelanggan']; // Contoh nilai jenis_pembelian
                $pesananData->harga_potongan = $returTmbhn['harga_potongan']; // Contoh nilai harga_potongan
                $pesananData->id_diskon = $diskonId; // Contoh nilai id_diskon







                // Update data barang
                // $stokTersedia = StokBarangModel::selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')->where('id_barang', $barangData->id_barang)->groupBy('id_barang')->first();
                $stokTersedia = $barangData;
                if ($stokTersedia->stok >=  $returTmbhn['jumlah_pesanan']) {
                    // $stokBarang = new StokBarangModel();
                    // $stokBarang->stok_keluar = $pesananData->jumlah_pembelian;
                    // $stokBarang->id_barang = $barangData->id_barang;
                    // $stokBarang->save();



                    $barangData->stok = $barangData->stok - $pesananData->jumlah_pembelian;
                    $barangData->save();
                    // Buat instance dari model
                    $stokbarangHistory = new StokBarangHistoryModel();
                    $stokbarangHistory->id_barang = $barangData->id_barang;
                    // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                    $stokbarangHistory->stok_keluar = $pesananData->jumlah_pembelian;
                    $stokbarangHistory->stok_terkini = $barangData->stok;
                    $stokbarangHistory->save();


                    // Simpan ke log
                    $logStokBarang = new LogStokBarangModel();
                    $logStokBarang->json_content = [
                        'type' => 'retur_pembeli_store',
                        'data' => [
                            'pelanggan' => $notaPembelian->id_pelanggan,
                            'no_nota' => $notaPembelian->no_nota,

                        ]
                    ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                    $logStokBarang->tipe_log = 'retur_pembeli_create';
                    $logStokBarang->keterangan = 'Tambah jumlah pesanan  ' . $notaPembelian->no_nota . ' pada pelanggan ' . $notaPembelian->Pembeli->nama_pembeli;

                    $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                    // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                    $logStokBarang->id_barang = $barangData->id_barang; // Sesuaikan dengan id_barang yang ada
                    $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                    $logStokBarang->save();


                    // $pesananData->id_stokbarang = $stokBarang->id;
                } else {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Gagal memasukkan data');
                }
                // Simpan data ke database
                $pesananData->save();


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
        }




        // Perhitungan Sub Total dan Total
        // Menghitung lagi pesanan
        // Sub Total seluruhnya 
        $subTotal = 0;
        $totalDiskon = 0;
        $notaPembeliPesanan = NotaPembeli::with('PesananPembeli')->find($notaPembelian->id_nota);



        foreach ($notaPembeliPesanan->PesananPembeli as $pesananPembeli3) {


            $subTotal += $pesananPembeli3->harga *  $pesananPembeli3->jumlah_pembelian;
            $totalDiskon += $pesananPembeli3->diskon;
        }


        $nominalTerbayarOld = $notaPembeliPesanan->nominal_terbayar;
        $totalOld = $notaPembeliPesanan->total;


        $notaPembeliPesanan->sub_total = $subTotalbaru + $subTotalReturMurni;

        $nilaiTotal = $notaPembeliPesanan->sub_total - $notaPembeliPesanan->diskon;

        $notaPembeliPesanan->nominal_terbayar = $request->nominal_terbayar;
        // $nilaiPajak = $nilaiTotal * ( $notaPembeliPesanan->pajak / 100);
        // $notaPembeliPesanan->total = $nilaiTotal + $nilaiPajak;
        $notaPembeliPesanan->total = $nilaiTotal + $notaPembeliPesanan->ongkir;
        $notaPembeliPesanan->save();


        // Perhitungan Kembali untuk laporan Piutang untuk hutang dan lunas
        if ($totalOld == $nominalTerbayarOld) {
            // Lunas ke lunas 
            $total_baru = $notaPembeliPesanan->total;
            $nominal_terbayar_baru = $notaPembeliPesanan->nominal_terbayar;
            if ($total_baru == $nominal_terbayar_baru) {
                // Update pada bukubesar
                $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $bukuBesar->save();
            }

            // Lunas ke hutang
            else {

                // Update pada bukubesar
                $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $bukuBesar->save();


                // Hapus seluruh bukubesar yang setelah edit
                $notaBukuBesarList = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                $notaBukuBesarList->skip(1)->each(function ($notaBukuBesar) {
                    $notaBukuBesar->delete();
                });
            }
        } else {

            $notaPembeliCheck = NotaPembeli::where('id_nota', $notaPembeliPesanan->id_nota)->first();
            $total_baru = $notaPembeliCheck->total;
            $nominal_terbayar_baru = $notaPembeliCheck->nominal_terbayar;

            // Hutang ke lunas
            if ($total_baru == $nominal_terbayar_baru) {

                // Update pada bukubesar
                $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $bukuBesar->save();


                // Hapus seluruh bukubesar yang setelah edit
                $notaBukuBesarList = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                $notaBukuBesarList->skip(1)->each(function ($notaBukuBesar) {
                    $notaBukuBesar->delete();
                });

                // Hutang ke hutang
            } else if ($totalOld != $total_baru || $nominal_terbayar_baru !=  $nominalTerbayarOld) {
                // Update pada bukubesar
                $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->first();
                $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
                $bukuBesar->save();


                // Hapus seluruh bukubesar yang setelah edit
                $notaBukuBesarList = Notabukubesar::where('id_nota', $notaPembeliPesanan->id_nota)->get();
                $notaBukuBesarList->skip(1)->each(function ($notaBukuBesar) {
                    $notaBukuBesar->delete();
                });
            }
        }




        // Asumsikan $notaPembeli adalah instance dari model NotaPembeli yang sudah ada
        $notaPembeliToSave = NotaPembeli::with('PesananPembeli')->find($notaPembeliPesanan->id_nota)->toArray();
        $logNota = new LogNotaModel();
        $logNota->json_content = $notaPembeliToSave;
        $logNota->tipe_log = 'retur_pembeli_create';
        $logNota->keterangan = 'Retur Pembelian';
        $logNota->id_nota = $notaPembeliPesanan->id_nota;
        $logNota->id_admin = Auth::user()->id_admin; // Mengambil id_admin dari user yang sedang login
        $logNota->save();

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
        DB::beginTransaction();

        try {
            $dataReturPembeli = ReturPembeliModel::find($this->hashToId($id_retur)->id_retur_pembeli);

            // Ambil semua pesanan pembeli yang terkait dengan retur ini
            $returPesananPembeli = ReturPesananPembeliModel::where('id_retur_pembeli', $dataReturPembeli->id_retur_pembeli)
                ->latest('id_retur_pesanan')
                ->get();
            // Untuk mengambil total lama dan nominal_terbayar lama sebelum diupdate

            $notaPembeli = NotaPembeli::find($dataReturPembeli->id_nota);
            $total_lama = $notaPembeli->total;
            $nominal_terbayar_lama = $notaPembeli->nominal_terbayar;

            // dd([
            //     'total_lama' => $total_lama,
            //     'nominal_terbayar' => $nominal_terbayar_lama
            // ]);


            foreach ($returPesananPembeli as $returPesanan) {
                $pesananPembeli = PesananPembeli::withTrashed()->find($returPesanan->id_pesanan_pembeli);

                switch ($returPesanan->type_retur_pesanan) {
                    case 'retur_murni_tidak_rusak':
                        $pesananPembeli->jumlah_pembelian = $returPesanan->qty_sebelum_perubahan;
                        $pesananPembeli->harga = $returPesanan->harga;
                        $pesananPembeli->save();

                        // Update Stok barang
                        // $stokBarang = StokBarangModel::find($pesananPembeli->id_stokbarang);
                        // $stokBarang->stok_keluar = $pesananPembeli->jumlah_pembelian;
                        // $stokBarang->save();







                        $barang = Barang::find($pesananPembeli->id_barang);
                        $barang->stok = $barang->stok - $returPesanan->qty;
                        $barang->save();

                        // Buat log jika qty tidak sama dengan 0
                        if ($returPesanan->qty != 0) {
                            // Buat instance dari model
                            $stokbarangHistory = new StokBarangHistoryModel();
                            $stokbarangHistory->id_barang = $barang->id_barang;
                            $stokbarangHistory->stok_keluar = $returPesanan->qty;
                            $stokbarangHistory->stok_terkini = $barang->stok;
                            $stokbarangHistory->save();
                            // Simpan ke log
                            $logStokBarang = new LogStokBarangModel();
                            $logStokBarang->json_content = [
                                'type' => 'pembelian_store',
                                'data' => [
                                    'pelanggan' => $notaPembeli->id_pelanggan,
                                    'no_nota' => $notaPembeli->no_nota,
                                ]
                            ]; // Sesuaikan dengan isi json_content Anda
                            $logStokBarang->tipe_log = 'retur_pembeli_delete';
                            $logStokBarang->keterangan = 'Hapus retur pesanan tidak rusak ' . $notaPembeli->no_nota . ' pada pelanggan ' . $notaPembeli->Pembeli->nama_pembeli;

                            $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                            // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                            $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
                            $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                            $logStokBarang->save();
                        }
                        break;

                    case 'retur_murni_rusak':
                        $pesananPembeli->jumlah_pembelian = $returPesanan->qty_sebelum_perubahan;
                        $pesananPembeli->harga = $returPesanan->harga;
                        $pesananPembeli->save();
                        // Update Stok Barang
                        // $stokBarang = StokBarangModel::withTrashed()->find($pesananPembeli->id_stokbarang);
                        // $stokBarang->stok_keluar =  $pesananPembeli->jumlah_pembelian;
                        // $stokBarang->save();







                        // $barang = Barang::find($pesananPembeli->id_barang);
                        // $barang->stok = $barang->stok;
                        // $barang->save();

                        // // Buat instance dari model
                        // $stokbarangHistory = new StokBarangHistoryModel();
                        // $stokbarangHistory->id_barang = $barang->id_barang;
                        // // $stokbarangHistory->stok_masuk = $pesananPembeli->jumlah_pembelian;
                        // // $stokbarangHistory->stok_keluar = $item['qty'];
                        // $stokbarangHistory->stok_terkini = $barang->stok;
                        // $stokbarangHistory->save();






                        // // Simpan ke log
                        // $logStokBarang = new LogStokBarangModel();
                        // $logStokBarang->json_content = [
                        //     'type' => 'retur_pembeli_destroy',
                        //     'data' => [
                        //         'pelanggan' => $notaPembeli->id_pelanggan,
                        //         'no_nota' => $notaPembeli->no_nota,

                        //     ]
                        // ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                        // $logStokBarang->tipe_log = 'retur_pembeli_delete';
                        // $logStokBarang->keterangan = 'Hapus retur pesanan rusak ' . $notaPembeli->no_nota . ' pada pelanggan ' . $notaPembeli->Pembeli->nama_pembeli;

                        // $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                        // // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                        // $logStokBarang->id_barang = $pesananPembeli->id_barang; // Sesuaikan dengan id_barang yang ada
                        // $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                        // $logStokBarang->save();
                        break;

                    case 'retur_tambah_stok':
                        $pesananPembeli->jumlah_pembelian = $returPesanan->qty_sebelum_perubahan;
                        $pesananPembeli->harga = $returPesanan->harga;
                        $pesananPembeli->save();

                        // Update Stok Barang
                        // $stokBarang = StokBarangModel::find($pesananPembeli->id_stokbarang);
                        // $stokBarang->stok_keluar =  $pesananPembeli->jumlah_pembelian;
                        // $stokBarang->save();






                        // New Stok Barang History
                        $barang = Barang::find($pesananPembeli->id_barang);

                        // dd([
                        //     $barang->stok,
                        //     $returPesanan->qty
                        // ]);
                        $barang->stok = $barang->stok +  $returPesanan->qty;
                        $barang->save();
                        // Buat instance dari model
                        $stokbarangHistory = new StokBarangHistoryModel();
                        $stokbarangHistory->id_barang = $barang->id_barang;
                        $stokbarangHistory->stok_masuk =  $returPesanan->qty;
                        // $stokbarangHistory->stok_keluar = $item['qty'];
                        $stokbarangHistory->stok_terkini = $barang->stok;
                        $stokbarangHistory->save();



                        // Simpan ke log
                        $logStokBarang = new LogStokBarangModel();
                        $logStokBarang->json_content = [
                            'type' => 'retur_pembeli_destroy',
                            'data' => [
                                'pelanggan' => $notaPembeli->id_pelanggan,
                                'no_nota' => $notaPembeli->no_nota,

                            ]
                        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                        $logStokBarang->tipe_log = 'retur_pembeli_delete';
                        $logStokBarang->keterangan = 'Hapus retur pesanan tambah jumlah  ' . $notaPembeli->no_nota . ' pada pelanggan ' . $notaPembeli->Pembeli->nama_pembeli;

                        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                        $logStokBarang->id_barang = $pesananPembeli->id_barang; // Sesuaikan dengan id_barang yang ada
                        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                        $logStokBarang->save();

                        break;

                    case 'retur_tambah_barang':
                        $pesananPembeli->jumlah_pembelian = $returPesanan->qty_sebelum_perubahan;
                        $pesananPembeli->harga = $returPesanan->harga;
                        $pesananPembeli->save();

                        // Hapus stok barang
                        // $stokBarang = StokBarangModel::find($pesananPembeli->id_stokbarang);


                        // New Stok Barang History
                        $barang = Barang::find($pesananPembeli->id_barang);

                        $barang->stok = $barang->stok + $pesananPembeli->jumlah_pembelian;
                        $barang->save();
                        // Buat instance dari model
                        $stokbarangHistory = new StokBarangHistoryModel();
                        $stokbarangHistory->id_barang = $barang->id_barang;
                        $stokbarangHistory->stok_masuk = $pesananPembeli->jumlah_pembelian;
                        // $stokbarangHistory->stok_keluar = $item['qty'];
                        $stokbarangHistory->stok_terkini = $barang->stok;
                        $stokbarangHistory->save();
                        // Simpan ke log
                        $logStokBarang = new LogStokBarangModel();
                        $logStokBarang->json_content = [
                            'type' => 'retur_pembeli_destroy',
                            'data' => [
                                'pelanggan' => $notaPembeli->id_pelanggan,
                                'no_nota' => $notaPembeli->no_nota,

                            ]
                        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                        $logStokBarang->tipe_log = 'retur_pembeli_delete';
                        $logStokBarang->keterangan = 'Hapus retur pesanan tambah barang di nota ' . $notaPembeli->no_nota . ' pada pelanggan ' . $notaPembeli->Pembeli->nama_pembeli;

                        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
                        $logStokBarang->id_barang = $pesananPembeli->id_barang; // Sesuaikan dengan id_barang yang ada
                        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
                        $logStokBarang->save();

                        // $stokBarang->delete();



                        // Jika jumlah_pembelian menjadi 0, hapus pesanan
                        if ($pesananPembeli->jumlah_pembelian == 0) {
                            $pesananPembeli->delete();
                        }
                        break;
                }
                // Jika pesanan pembeli dihapus dan jumlah pembelian lebih dari 0, lakukan restore
                if ($pesananPembeli->trashed() && $pesananPembeli->jumlah_pembelian > 0) {
                    $pesananPembeli->restore();

                    // Find the stock item related to the restored order, including trashed items
                    // $stokBarang = StokBarangModel::withTrashed()->find($pesananPembeli->id_stokbarang);
                    // If the stock item was trashed, restore it as well
                    // if ($stokBarang && $stokBarang->trashed()) {
                    //     $stokBarang->restore();
                    // }

                    // Update the stock quantity based on the order quantity
                    // if ($stokBarang) {
                    //     $stokBarang->stok_keluar += $pesananPembeli->jumlah_pembelian;
                    //     $stokBarang->save();
                    // }
                }
                // Hapus retur pesanan pembeli setelah memprosesnya
                $returPesanan->delete();
            }



            // Hapus data retur pembeli
            $dataReturPembeli->delete();


            // Hitung lagi Nota Pembeli
            $notaPembeli = NotaPembeli::with('PesananPembeli')->find($dataReturPembeli->id_nota);
            $subTotal = 0;

            foreach ($notaPembeli->PesananPembeli as $pesananPembeli) {
                $subTotal  += $pesananPembeli->harga *  $pesananPembeli->jumlah_pembelian;
            }


            // Menghitung kembali total dari pesanan
            $updateNotaPembeli = NotaPembeli::with('bukuBesar')->find($notaPembeli->id_nota);

            $updateNotaPembeli->sub_total = $subTotal;

            // Perhitungan Pajak
            $nilaiTotal = $updateNotaPembeli->sub_total - $updateNotaPembeli->diskon;
            // $nilaiPajak = $nilaiTotal * ( $updateNotaPembeli->pajak / 100);
            $nilaiOngkir =  $updateNotaPembeli->ongkir;
            $updateNotaPembeli->total = $nilaiTotal + $nilaiOngkir;
            $updateNotaPembeli->save();





            // dd($total_lama == $nominal_terbayar_lama);

            // Menghitung jika lunas maka otomatis nominal terbayar langsung mengisi bukubesar pertama jika hutang maka hapus seluruh bukubesar lalu hitung lagi
            if ($total_lama == $nominal_terbayar_lama) {
                $notaPembeli1 = NotaPembeli::find($notaPembeli->id_nota);
                $notaPembeli1->nominal_terbayar = $notaPembeli1->total;
                $notaPembeli1->save();


                // Update pada bukubesar
                $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeli1->id_nota)->first();
                $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                $bukuBesar->debit = $notaPembeli1->total;
                $bukuBesar->save();
            } else {
                $notaPembeliCheck = NotaPembeli::where('id_nota', $notaPembeli->id_nota)->first();
                if ($nominal_terbayar_lama != $notaPembeli->nominal_terbayar) {

                    // Update pada bukubesar
                    $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeli->id_nota)->first();
                    $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                    $bukuBesar->debit = $notaPembeli->nominal_terbayar;
                    $bukuBesar->save();


                    // Hapus seluruh bukubesar yang setelah edit
                    $notaBukuBesarList = Notabukubesar::where('id_nota', $notaPembeli->id_nota)->get();
                    $notaBukuBesarList->skip(1)->each(function ($notaBukuBesar) {
                        $notaBukuBesar->delete();
                    });
                } else if ($notaPembeliCheck->total != $total_lama) {
                    // Update pada bukubesar
                    $notaBukuBesar = Notabukubesar::where('id_nota', $notaPembeli->id_nota)->first();
                    $bukuBesar = BukubesarModel::find($notaBukuBesar->id_bukubesar);
                    $bukuBesar->debit = $notaPembeli->nominal_terbayar;
                    $bukuBesar->save();


                    // Hapus seluruh bukubesar yang setelah edit
                    $notaBukuBesarList = Notabukubesar::where('id_nota', $notaPembeli->id_nota)->get();
                    $notaBukuBesarList->skip(1)->each(function ($notaBukuBesar) {
                        $notaBukuBesar->delete();
                    });
                }
            }




            // Asumsikan $notaPembeli adalah instance dari model NotaPembeli yang sudah ada
            $notaPembeliToSave = NotaPembeli::with('PesananPembeli')->find($notaPembeli->id_nota)->toArray();
            $logNota = new LogNotaModel();
            $logNota->json_content = $notaPembeliToSave;
            $logNota->tipe_log = 'retur_pembeli_revoke';
            $logNota->keterangan = 'Mengembalikan retur pembeli';
            $logNota->id_nota = $notaPembeli->id_nota;
            $logNota->id_admin = Auth::user()->id_admin; // Mengambil id_admin dari user yang sedang login
            $logNota->save();


            DB::commit();
            return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

     
     public function hide($id_retur)
     {
         $retur = ReturPembeliModel::where('hash_id_retur_pembeli', $id_retur)->firstOrFail();
         $retur->hidden = 'yes';
         $retur->save();
 

         return redirect()->route('retur.index')->with('success', 'Retur berhasil disembunyikan');

     }
}
