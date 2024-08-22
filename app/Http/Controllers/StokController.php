<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\RiwayatHutangModel;
use App\Models\BukubesarModel;
use App\Models\HutangDanStokModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\PemasokBarang;
use App\Models\StokBarangHistoryModel;
use App\Models\StokBarangModel;
use App\Models\TipeBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    public function index()
    {
        $dataSemuaBarang = Barang::with('pemasok', 'tipeBarang', 'stokBarang')->get();
        $dataBaruSemuaBarang = [];
        foreach ($dataSemuaBarang as $barang) {
            // $totalStok = $barang->stokBarang->sum('stok_masuk') - $barang->stokBarang->sum('stok_keluar');
            // $barang->stok = $totalStok;
            $dataBaruSemuaBarang[] = $barang;
        }


        $dataTipeBarang = TipeBarang::all();
        $dataPemasok = PemasokBarang::all();

        $lastId = Barang::max('id_barang');
        $lastId = $lastId ? $lastId : 0; // handle jika tabel kosong
        $lastId++;


        $kode_barang = 'BRG' . date('Y') . date('mdHis') . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        return view('stokbarang.index', ['dataSemuaBarang' => $dataBaruSemuaBarang, 'dataPemasok' => $dataPemasok, 'dataTipeBarang' => $dataTipeBarang, 'kodeBarang' => $kode_barang]);

        // return view('stokbarang.index');
    }
    public function edit(Request $request, $id)
    {
        $dataBarang = Barang::where('hash_id_barang', $id)->first();

        if ($dataBarang) {
            // Mengambil jumlah total stok barang
            // $stokbarang = StokBarangModel::where('id_barang', $dataBarang->id_barang)
            //     ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
            //     ->whereNull('deleted_at')
            //     ->first();

            // Menambahkan jumlah stok ke dalam data barang
            // $dataBarang->stok = $stokbarang->stok;

            // $stokbarang = StokBarangModel::where('id_barang', $dataBarang->id_barang)->first();
            // $dataBarang->stokoriginal = $stokbarang->stok_masuk;
            $dataBarang->stokoriginal = $dataBarang->stok;
        }
        $dataTipeBarang = TipeBarang::all();
        $dataPemasok = PemasokBarang::all();
        if (!$dataBarang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('stokbarang.edit', compact('dataBarang', 'dataTipeBarang', 'dataPemasok'))->render()
        ], 200);
    }
    public function store(Request $request)
    {

        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'harga_barang' => 'required',
            'harga_barang_pemasok' => 'required',
            'stok' => 'required',
            'ukuran' => 'required',
            'tenggat_bayar' => 'required|date',
            // 'id_pemasok' => 'required',
            'id_tipe_barang' => 'required',
        ]);






        DB::beginTransaction();
        // Hitung total hutang barang



        // Total Kredit dari Stok Barang
        // Buat record baru untuk Barang
        $barang = new Barang();
        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->harga_barang =  $request->harga_barang;
        $barang->harga_barang_pemasok =  $request->get('harga_barang_pemasok');
        // $barang->stok = $request->stok;
        $barang->ukuran = $request->ukuran;
        $barang->id_pemasok = $request->id_pemasok;
        $barang->id_tipe_barang = $request->id_tipe_barang;
        $barang->total = $barang->harga_barang_pemasok * $request->stok;
        // $barang->dp_barang =  $request->get('nominal_terbayar');
        // $barang->nominal_terbayar =  $request->get('nominal_terbayar');
        $barang->tenggat_bayar = $request->get('tenggat_bayar');
        $barang->stok = $request->stok;
        $barang->stok_seluruh = $request->stok;



        $barang->save();

        // Periksa kondisi untuk tanggal penyelesaian
        if ($barang->nominal_terbayar == $barang->total && is_null($barang->tanggal_penyelesaian)) {
            $barang->tanggal_penyelesaian = $barang->updated_at;  // Atau $barang->updated_at jika diperlukan
            $barang->save();
        } elseif ($barang->nominal_terbayar != $barang->total && !is_null($barang->tanggal_penyelesaian)) {
            $barang->tanggal_penyelesaian = null;
            $barang->save();
        }

        // Buat record baru untuk BukuBesar
        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();

        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();
        $bukuBesar = new BukubesarModel();

        $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        $bukuBesar->keterangan = "Tambah Stok Barang " . $barang->nama_barang; // Isi dengan keterangan yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        $bukuBesar->debit =  $request->get('nominal_terbayar'); // Isi dengan nilai kredit yang sesuai
        $bukuBesar->save();


        // Update id bukubesar;
        // $barang->id_bukubesar = $bukuBesar->id_bukubesar;

        $barang->save();


        // Hutang dan Stok Barang
        $hutangDanStokBarangController = new HutangDanStokController();
        $statusStokBarang = $hutangDanStokBarangController->store([
            'harga_beli' => $request->harga_barang,
            'total' => $barang->total,
            'dp' => $request->get('nominal_terbayar'),
            'nominal_terbayar' => $barang->nominal_terbayar ?? 0,
            'stok' => $barang->stok,
            'tenggat_bayar' => $barang->tenggat_bayar,
            'id_bukubesar' => $bukuBesar->id_bukubesar,
            'id_barang' => $barang->id_barang,
        ]);

        if ($statusStokBarang['status'] === 'error') {
            // Jika terjadi error, rollback dan redirect back dengan pesan error
            DB::rollback();
            return redirect()->back()->with('error', $statusStokBarang['message'])->with('detail_error', $statusStokBarang['detail_error']);
        }


        // Buat instance dari model
        $stokbarangHistory = new StokBarangHistoryModel();
        $stokbarangHistory->id_barang = $barang->id_barang;
        $stokbarangHistory->stok_masuk = $request->stok;
        $stokbarangHistory->stok_terkini = $barang->stok;
        $stokbarangHistory->save();

        // $stokBarang = StokBarangModel::create([
        //     'stok_masuk' => $request->stok,
        //     'id_barang' => $barang->id_barang,
        //     'tipe_stok' => 'stokbarang'
        // ]);






        // $bukubesarBarang = new RiwayatHutangModel();
        // $bukubesarBarang->id_barang = $barang->id_barang;
        // $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;


        // $bukubesarBarang->save();



        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = [
            'type' => 'stok_store',
            'data' => []
        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_create';
        $logStokBarang->keterangan = 'Tambah barang ke stok dengan total stok awal ' . $request->stok;
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
        $logStokBarang->save();
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data  Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            // 'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            // 'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            // 'id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            // 'stok' => 'required|min:0',
            'harga_barang' => 'required|numeric|min:0',
            // 'harga_barang_pemasok' => 'required|numeric|min:0',
            // 'status_pembelian' => 'required|in:lunas,hutang',
            // 'nominal_terbayar' => 'nullable|numeric|min:0',
            // 'tenggat_bayar' => 'nullable|date',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        // Mencari barang berdasarkan hash_id_barang
        $barang = Barang::with('bukuBesar')->where('hash_id_barang', $id)->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }

        // Update data barang
        $barang->id_pemasok = $request->id_pemasok;
        // $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->ukuran = $request->ukuran;
        $barang->id_tipe_barang = $request->id_tipe_barang;
        $barang->harga_barang = $request->harga_barang;
        $barang->harga_barang_pemasok = $request->harga_barang_pemasok;
        // $oldBarang = $barang->toArray();
        // $total_lama = $barang->total;
        // $nominal_terbayar_lama =  $barang->nominal_terbayar;
        // $barang->nominal_terbayar = $request->nominal_terbayar;
        // $barang->dp_barang = $request->dp;

        $barang->tenggat_bayar = $request->tenggat_bayar;
        // $barang->stok = $request->stok;

        $barang->save();







        // dd([
        //     'nominal_terbayar_lama' => $nominal_terbayar_lama,
        //     'nominal_terbayar_baru' => $request->nominal_terbayar,
        //     'total' => $total_lama
        // ]);




        // Menghitung total stok
        // $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
        //     ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
        //     ->first();

        // Stok Lama 
        // $stokLama = $stokBarang->stok;
        $stokLama = $barang->stok;

        $stokRequest = $request->stok;
        if ($request->has('toggleAdditionalFields')) {


            $selisihStokReqdanAsli = $stokRequest  - $stokLama;



            // $stokBaru = $stokBarangubahStok->stok_masuk;
            $stokBaru = $stokLama + $selisihStokReqdanAsli;

            // Stok Baru




            // Update total barang setelah mengubah stok masuk
            $updatebarangtotal = Barang::find($barang->id_barang);
            $updatebarangtotal->stok = $stokRequest;
            $updatebarangtotal->stok_seluruh =  $updatebarangtotal->stok;
            $updatebarangtotal->harga_barang_pemasok = $request->harga_barang_pemasok;
            $updatebarangtotal->tenggat_bayar = $request->tenggat_bayar;

            $updatebarangtotal->total = $updatebarangtotal->stok_seluruh * $updatebarangtotal->harga_barang_pemasok;
            $updatebarangtotal->save();



            // Buat instance dari model
            $stokbarangHistory = new StokBarangHistoryModel();
            $stokbarangHistory->id_barang = $barang->id_barang;
            if ($selisihStokReqdanAsli < 0) {
                $stokbarangHistory->stok_keluar = abs($selisihStokReqdanAsli); // Convert to positive and assign to stok_keluar
            } else {
                $stokbarangHistory->stok_masuk = $selisihStokReqdanAsli; // Assign to stok_masuk
            }
            $stokbarangHistory->stok_terkini = $updatebarangtotal->stok;
            $stokbarangHistory->save();






            // Simpan ke log
            $logStokBarang = new LogStokBarangModel();
            $logStokBarang->json_content = [
                'type' => 'stok_update',
                'data' => [
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,

                ]
            ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
            $logStokBarang->tipe_log = 'barang_update';
            $logStokBarang->keterangan = 'Update barang dari ' . $stokLama . ' ke ' . $stokBaru;
            $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
            // $logStokBarang->id_stok_barang = $stokBarangubahStok->id; // Sesuaikan dengan id_stok_barang yang ada
            $logStokBarang->id_barang = $updatebarangtotal->id_barang; // Sesuaikan dengan id_barang yang ada
            $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
            $logStokBarang->save();


            $bukuBesarUpdate = new BukubesarModel();

            $bukuBesarUpdate->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
            $bukuBesarUpdate->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
            $bukuBesarUpdate->kategori = "barang"; // Isi dengan kategori yang sesuai
            $bukuBesarUpdate->keterangan = "Update Stok Barang " . $barang->nama_barang; // Isi dengan keterangan yang sesuai
            // $bukuBesarUpdate->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
            $bukuBesarUpdate->debit = $request->dp; // Isi dengan nilai kredit yang sesuai
            $bukuBesarUpdate->save();



            // Hapus lalu reset lagi
            $hutangDanStok = $barang->hutangDanStok;
            foreach ($hutangDanStok as $hutangStok) {
                $hutangStokData = HutangDanStokModel::find($hutangStok->id_hutang_stok);
                $hutangStokData->delete();
            }

            // Hutang dan Stok Barang
            $hutangDanStokBarangController = new HutangDanStokController();
            $statusStokBarang = $hutangDanStokBarangController->store([
                'harga_beli' => $request->harga_barang,
                'total' => $barang->total,
                'dp' => $request->dp,
                'nominal_terbayar' =>  0,
                'stok' => $barang->stok,
                'tenggat_bayar' => $barang->tenggat_bayar,
                'id_bukubesar' => $bukuBesarUpdate->id_bukubesar,
                'id_barang' => $barang->id_barang,
            ]);

            if ($statusStokBarang['status'] === 'error') {
                // Jika terjadi error, rollback dan redirect back dengan pesan error
                DB::rollback();
                return redirect()->back()->with('error', $statusStokBarang['message'])->with('detail_error', $statusStokBarang['detail_error']);
            }
        }





        // Update Total
        $updateTotal = Barang::find($barang->id_barang);
        $updateTotal->total = $updateTotal->stok * $updateTotal->harga_barang_pemasok;
        $updateTotal->save();


        // $handleRiwayatHutang = self::handleRiwayatHutang($oldBarang, $request);

        // if (!$handleRiwayatHutang) {
        //     return redirect()->back()->with(['error' => 'Terjadi Kesalahan pada sisi cicilan']);
        // }

        // dd("atas");
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data barang berhasil diperbarui');
    }


    public function destroy($id)
    {
        $dataBarang = Barang::with('riwayatHutang', 'hutangDanStok')->where('hash_id_barang', $id)->first();
        if ($dataBarang) {

            // hapus riwayatHutang
            foreach ($dataBarang->riwayatHutang as $riwayatHutang) {
                $riwayatHutang = RiwayatHutangModel::find($riwayatHutang->id);
                $riwayatHutang->delete();
            }

            foreach ($dataBarang->hutangDanStok as $hutangDanStok) {

                $hutangDanStok = HutangDanStokModel::find($hutangDanStok->id_hutang_stok);
                $hutangDanStok->delete();
            }

            $dataBarang->delete();

            return redirect()->route('stok.index')->with('success', 'Barang berhasil dihapus');
        } else {
            return redirect()->route('stok.index')->with('error', 'Barang gagal dihapus');
        }
    }


    public function showStokBarang($id)
    {

        $dataBarang = Barang::with([
            'stokBarang',
            'hutangDanStok' => function ($query) {
                $query->select([
                    'id_hutang_stok as id',
                    'stok as total_stok',
                    'created_at as tanggal_dibuat',
                    'id_barang'
                ]);
            }
        ])->where('hash_id_barang', $id)->first();

        if (!$dataBarang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }

        $dataBarangBaru = [];
        // $totalStok = $dataBarang->stokBarang->sum('stok_masuk') - $dataBarang->stokBarang->sum('stok_keluar');
        // $dataBarang->stok = $totalStok;

        $dataBarangBaru = $dataBarang;
        // print_r($dataBarang);
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $dataBarangBaru
        ], 200);
    }

    public function addStock(Request $request)
    {


        $validatedData = $request->validate([
            'stok_tambah' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barangs,hash_id_barang',
            'dp' => 'required',

        ], [
            'stok_tambah.required' => 'Stok tambah harus diisi.',
            'stok_tambah.numeric' => 'Stok tambah harus berupa angka.',
            'stok_tambah.min' => 'Stok tambah harus lebih dari atau sama dengan 0.',
            'id_barang.exists' => 'Barang tidak ditemukan.',
        ]);

        $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($validatedData['stok_tambah'] <= 0) {
            return redirect()->back()->with('error', 'Penambahan tidak valid.');
        }

        DB::beginTransaction();
        // $barang->stok += $validatedData['stok_tambah'];

        // Kembalikan jika barang tidak ada
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }



        // Menghitung total stok
        // $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)->first();


        // Jumlah Stok Final Stok dari input
        // $stoktambah = $validatedData['stok_tambah'] +  $stokBarang->stok_masuk;
        // $stokBarang->stok_masuk = $stoktambah;


        // $stokBarang->save();

        // Update total
        $barang->stok += $validatedData['stok_tambah'];
        $barang->stok_seluruh += $validatedData['stok_tambah'];
        $total = $validatedData['stok_tambah'] * $barang->harga_barang_pemasok;
        $barang->total +=  $total;

        // if($request->nominal_terbayar == $total)
        // {

        // }
        // dd($request->nominal_terbayar);
        // $barang->nominal_terbayar += $request->nominal_terbayar;
        $barang->save();









        // updpate Hutang
        // Buat Bukubesar
        $updateBukuBesar = new BukubesarModel();
        $updateBukuBesar->id_akunbayar = 1;
        $updateBukuBesar->tanggal = date('Y-m-d');
        $updateBukuBesar->kategori =  'barang';
        $updateBukuBesar->keterangan = 'Hutang';


        $updateBukuBesar->debit = $request->dp; // Masukkan nilai debit yang sesuai
        $updateBukuBesar->kredit = 0; // Jika debit maka kredit harus 0
        $updateBukuBesar->save();
        // RiwayatHutangModel::create([
        //     'id_barang' => $barang->id_barang,
        //     'id_bukubesar' => $updateBukuBesar->id_bukubesar,
        //     'nominal_dibayar' =>  $request->nominal_terbayar
        // ]);



        // Hutang dan Stok Barang
        $hutangDanStokBarangController = new HutangDanStokController();
        $statusStokBarang = $hutangDanStokBarangController->store([
            'total' =>   $total,
            'dp' => $request->dp,
            'nominal_terbayar' =>  0,
            'stok' => $validatedData['stok_tambah'],
            'tenggat_bayar' => $request->tenggat_bayar,
            'id_bukubesar' => $updateBukuBesar->id_bukubesar,
            'id_barang' => $barang->id_barang,
        ]);

        if ($statusStokBarang['status'] === 'error') {
            // Jika terjadi error, rollback dan redirect back dengan pesan error
            DB::rollback();
            return redirect()->back()->with('error', $statusStokBarang['message'])->with('detail_error', $statusStokBarang['detail_error']);
        }

        // $stoktambah = $validatedData['stok_tambah'];


        // Buat instance dari model
        $stokbarangHistory = new StokBarangHistoryModel();
        $stokbarangHistory->id_barang = $barang->id_barang;
        $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
        $stokbarangHistory->stok_terkini = $barang->stok;
        $stokbarangHistory->keterangan_stok_history = $request->keterangan ?? null;
        $stokbarangHistory->save();




        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = [
            'type' => 'pembelian_store',
            'data' => []
        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_tambah_stok';
        $logStokBarang->keterangan = 'Tambah stok barang sebanyak ' . $validatedData['stok_tambah'];
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
        $logStokBarang->save();







        // Buat record baru untuk BukuBesar
        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang_tambah_stok"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'TAMBAH STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->nominal_terbayar; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();

        // $bukubesarBarang = new RiwayatHutangModel();
        // $bukubesarBarang->id_barang = $barang->id_barang;
        // $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;
        // $bukubesarBarang->save();

        // // Hitung lagi nominal terbayar stok 
        // $barangupdated = Barang::with('bukuBesar')->find($barang->id_barang);
        // $totalNominalTerbayar = 0;
        // foreach ($barangupdated->bukuBesar as $bukuBesar) {
        //     $totalNominalTerbayar += $bukuBesar->debit;
        // }

        // $barangupdated->nominal_terbayar = $totalNominalTerbayar;
        // $barangupdated->save();


        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }
    public function minusStok(Request $request)
    {





        $validatedData = $request->validate([
            'stok_kurang' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barangs,hash_id_barang',
            'nominal_terbayar' => 'required',
            'hutang_stok' => 'required|exists:lacak_stok,id_hutang_stok'

        ], [
            'stok_kurang.required' => 'Stok tambah harus diisi.',
            'stok_kurang.numeric' => 'Stok tambah harus berupa angka.',
            'stok_kurang.min' => 'Stok tambah harus lebih dari atau sama dengan 0.',
            'id_barang.exists' => 'Barang tidak ditemukan.',
            'hutang_stok.exist' => 'Hutang stok tidak ditemukan.'
        ]);



        // dd($validatedData);
        $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($validatedData['stok_kurang'] <= 0) {
            return redirect()->back()->with('error', 'Pengurangan stok tidak valid.');
        }

        DB::beginTransaction();
        // $barang->stok += $validatedData['stok_tambah'];

        // Kembalikan jika barang tidak ada
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }



        // Menghitung total stok
        // $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)->first();


        // Jumlah Stok Final Stok dari input
        // $stoktambah = $validatedData['stok_tambah'] +  $stokBarang->stok_masuk;
        // $stokBarang->stok_masuk = $stoktambah;


        // $stokBarang->save();

        // Update total
        $barang->stok -=   $validatedData['stok_kurang'];
        $barang->stok_seluruh -=   $validatedData['stok_kurang'];
        $barang->total -= $validatedData['stok_kurang'] * $barang->harga_barang_pemasok;
        // $barang->nominal_terbayar -= $request->nominal_terbayar;
        $barang->save();



        // dd($validatedData['hutang_stok']);
        // Hutang dan Stok
        $updateHutangdanStok = HutangDanStokModel::find($validatedData['hutang_stok']);
        $updateHutangdanStok->total -= $validatedData['stok_kurang'] * $updateHutangdanStok->harga_beli;
        $updateHutangdanStok->stok -= $validatedData['stok_kurang'];
        $updateHutangdanStok->save();

        // $stoktambah = $validatedData['stok_kurang'];

        // Buat instance dari model
        $stokbarangHistory = new StokBarangHistoryModel();
        $stokbarangHistory->id_barang = $barang->id_barang;
        $stokbarangHistory->stok_keluar = $validatedData['stok_kurang'];
        $stokbarangHistory->stok_terkini = $barang->stok;
        $stokbarangHistory->keterangan_stok_history = $request->keterangan ?? null;
        $stokbarangHistory->save();

        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = [
            'type' => 'pembelian_stok',
            'data' => []
        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_kurang_stok';
        $logStokBarang->keterangan = 'Kurang  stok barang sebanyak ' . $validatedData['stok_kurang'];
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
        $logStokBarang->save();







        // // Buat record baru untuk BukuBesar
        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang_kurang_stok"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'KURANG STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->nominal_terbayar; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();

        // $bukubesarBarang = new RiwayatHutangModel();
        // $bukubesarBarang->id_barang = $barang->id_barang;
        // $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;
        // $bukubesarBarang->save();

        // // Hitung lagi nominal terbayar stok 
        // $barangupdated = Barang::with('bukuBesar')->find($barang->id_barang);
        // $totalNominalTerbayar = 0;
        // foreach ($barangupdated->bukuBesar as $bukuBesar) {
        //     $totalNominalTerbayar += $bukuBesar->debit;
        // }

        // $barangupdated->nominal_terbayar = $totalNominalTerbayar;
        // $barangupdated->save();


        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }


    // // Tidak Digunakan untuk saat ini
    // public static function  handleRiwayatHutang($barangold, $request)
    // {


    //     // DAri array ke instance model lagi

    //     $barangOld = new Barang($barangold);
    //     // Definisikan Id Nota Lagi
    //     $barangOld->id_barang = $barangold['id_barang'];

    //     $totalOld = $barangOld->total;
    //     $nominalTerbayarOld =  $barangOld->nominal_terbayar;
    //     $dpOld = $barangOld->dp_barang;

    //     $resetCicilan = $request->reset_cicilan ? 1 : 0;
    //     // Apakah direset cicilannya juga ?
    //     if ($resetCicilan) {

    //         // Perhitungan Kembali untuk laporan Piutang untuk hutang dan lunas
    //         if ($totalOld == ($nominalTerbayarOld + $dpOld)) {

    //             $barangCheck = Barang::where('id_barang', $barangOld->id_barang)->first();
    //             // Lunas ke lunas 
    //             $total_baru = $barangCheck->total;
    //             $nominal_terbayar_baru = $barangCheck->nominal_terbayar;
    //             $dp_baru = $barangCheck->dp_barang;

    //             // dd([
    //             //     $nominalTerbayarOld, $dpOld, $nominal_terbayar_baru, $dp_baru
    //             // ]);



    //             if ($total_baru == ($nominal_terbayar_baru + $dp_baru)) {
    //                 // Update pada bukubesar
    //                 // $RiwayatPiutangModel = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->first();
    //                 // $bukuBesar = BukubesarModel::find($RiwayatPiutangModel->id_bukubesar);
    //                 // $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
    //                 // $bukuBesar->save();
    //                 // $notaPembeliPesanan->nominal_terbayar = $notaPembeliPesanan->nominal_terbayar;
    //                 // Periksa kondisi untuk tanggal penyelesaian



    //                 // $notaPembeliPesanan->save();


    //                 $updateBukubesar = BukubesarModel::find($barangOld->id_bukubesar);
    //                 $updateBukubesar->debit = $barangOld->dp_barang;
    //                 $updateBukubesar->save();


    //                 // Update Tanggal Selesai
    //                 if (is_null($barangOld->tanggal_penyelesaian)) {
    //                     $barangCheck->tanggal_penyelesaian =  $barangCheck->updated_at;
    //                     $barangCheck->save();
    //                 }

    //                 // Reset Nominal terbayar 
    //                 $barangCheck->nominal_terbayar = 0;
    //                 // $barangCheck->hidden = 'yes';
    //                 $barangCheck->save();
    //                 // Reset List Piutang yang telah dibayar
    //                 $riwayatHutangList = RiwayatHutangModel::where('id_barang', $barangOld->id_barang)->get();
    //                 foreach ($riwayatHutangList as $riwayatHutang) {
    //                     $bukubesarRiwayatHutang = BukubesarModel::find($riwayatHutang->id_bukubesar);
    //                     $bukubesarRiwayatHutang->delete();
    //                     $riwayatHutang->delete();
    //                 }
    //             }

    //             // Lunas ke hutang
    //             else {






    //                 // RiwayatHutangModel::where('id_barang', $barang->id_barang)->delete();
    //                 // $notaPembeliPesanan->nominal_terbayar = $notaPembeliPesanan->nominal_terbayar;


    //                 // Rubah tanggal selesai Menjadi Hutang
    //                 if (!is_null($barangOld->tanggal_penyelesaian)) {
    //                     $barangCheck->tanggal_penyelesaian =  null;
    //                     $barangCheck->save();
    //                 }

    //                 // Periksa kondisi untuk tanggal penyelesaian
    //                 // if (($barangOldPesanan->nominal_terbayar + $barangOldPesanan->update) == $barangOldPesanan->total && is_null($barangOldPesanan->tanggal_penyelesaian)) {
    //                 //     $barangOldPesanan->tanggal_penyelesaian = $barangOldPesanan->updated_at;  // Atau $barangOldPesanan->updated_at jika diperlukan
    //                 // } elseif (($barangOldPesanan->nominal_terbayar + $barangOldPesanan->update) != $barangOldPesanan->total && !is_null($barangOldPesanan->tanggal_penyelesaian)) {
    //                 //     $barangOldPesanan->tanggal_penyelesaian = null;
    //                 // }

    //                 // $barangOldPesanan->save();



    //                 $updateBukubesar = BukubesarModel::find($barangOld->id_bukubesar);
    //                 $updateBukubesar->debit = $barangOld->dp_barang;
    //                 $updateBukubesar->save();





    //                 // Update pada bukubesar
    //                 // $RiwayatPiutangModel = RiwayatPiutangModel::where('id_nota', $notaPembeliPesanan->id_nota)->first();
    //                 // $bukuBesar = BukubesarModel::find($RiwayatPiutangModel->id_bukubesar);
    //                 // $bukuBesar->debit = $notaPembeliPesanan->nominal_terbayar;
    //                 // $bukuBesar->save();


    //                 // check apakah cicilan direset ?
    //                 // Reset Nominal terbayar 
    //                 $barangCheck->nominal_terbayar = 0;
    //                 // $barangCheck->piutang_is_visible = 'yes';
    //                 $barangCheck->save();
    //                 // Reset List Piutang yang telah dibayar
    //                 $riwayatHutangList = RiwayatHutangModel::where('id_barang', $barangOld->id_barang)->get();
    //                 foreach ($riwayatHutangList as $riwayatHutang) {
    //                     $bukuBesarRiwayatPiutang = BukubesarModel::find($riwayatHutang->id_bukubesar);
    //                     $bukuBesarRiwayatPiutang->delete();
    //                     $riwayatHutang->delete();
    //                 }
    //             }
    //         } else {




    //             $barangCheck = Barang::where('id_barang', $barangOld->id_barang)->first();
    //             $total_baru = $barangCheck->total;
    //             $nominal_terbayar_baru = $barangCheck->nominal_terbayar;
    //             $dp_baru = $barangCheck->dp_barang;

    //             // dd([
    //             //     'totalold' => $totalOld,
    //             //     'nominalOld' => $nominalTerbayarOld

    //             // ]);



    //             // Hutang ke lunas

    //             if ($total_baru == ($nominal_terbayar_baru + $dp_baru)) {




    //                 // $notaPembeli->nominal_terbayar = $notaPembeli->nominal_terbayar;
    //                 // $notaPembeli->save();



    //                 $updateBukubesar = BukubesarModel::find($barangOld->id_bukubesar);
    //                 $updateBukubesar->debit = $barangOld->dp_barang;
    //                 $updateBukubesar->save();



    //                 // Update Tanggal Selesai
    //                 if (is_null($barangOld->tanggal_penyelesaian)) {
    //                     $barangCheck->tanggal_penyelesaian =  $barangCheck->updated_at;
    //                     $barangCheck->save();
    //                 }


    //                 // Reset Nominal terbayar 
    //                 $barangCheck->nominal_terbayar = 0;
    //                 // $barangCheck->piutang_is_visible = 'yes';
    //                 $barangCheck->save();
    //                 // Reset List Piutang yang telah dibayar
    //                 $riwayatHutangList = RiwayatHutangModel::where('id_barang', $barangCheck->id_barang)->get();
    //                 foreach ($riwayatHutangList as $riwayatHutang) {
    //                     $bukuBesarRiwayatHutang = BukubesarModel::find($riwayatHutang->id_bukubesar);
    //                     $bukuBesarRiwayatHutang->delete();
    //                     $riwayatHutang->delete();
    //                 }

    //                 // Hutang ke hutang
    //             } else {
    //                 // } else if ($totalOld != $total_baru || ($nominal_terbayar_baru + $dp_baru) !=  $nominalTerbayarOld + $dpOld) {


    //                 // $barangOldPesanan->nominal_terbayar = $barangOldPesanan->nominal_terbayar;
    //                 // $barangOldPesanan->save();



    //                 $updateBukubesar = BukubesarModel::find($barangOld->id_bukubesar);
    //                 $updateBukubesar->debit = $barangOld->dp_barang;
    //                 $updateBukubesar->save();




    //                 // Rubah tanggal selesai Menjadi Hutang
    //                 if (!is_null($barangCheck->tanggal_penyelesaian)) {
    //                     $barangCheck->tanggal_penyelesaian =  null;
    //                     $barangCheck->save();
    //                 }




    //                 // Reset Nominal terbayar 
    //                 $barangCheck->nominal_terbayar = 0;
    //                 // $barangCheck->piutang_is_visible = 'yes';
    //                 $barangCheck->save();
    //                 // Reset List Piutang yang telah dibayar
    //                 $riwayatHutangList = RiwayatHutangModel::where('id_barang', $barangOld->id_barang)->get();
    //                 foreach ($riwayatHutangList as $riwayatHutang) {

    //                     $bukuBesarRiwayatHutang = BukubesarModel::find($riwayatHutang->id_bukubesar);
    //                     $bukuBesarRiwayatHutang->delete();
    //                     $riwayatHutang->delete();
    //                 }
    //             }
    //         }
    //     }











    //     // Cicilan tidak direset
    //     else {

    //         $status_pembayaran = $request->status_pembelian;

    //         // $barangOldCheck = notaPembeliData::where('id_nota',$id_nota)->first();


    //         if ($barangOld->total == ($barangOld->dp_barang + $barangOld->nominal_terbayar)) {
    //             // dd($barangOld);
    //             $barangCheck = Barang::where('id_barang', $barangOld->id_barang)->first();
    //             $totalBaru = $barangCheck->total;



    //             // Lunas ke Lunas
    //             if ($status_pembayaran == 'lunas') {
    //                 // dd([
    //                 //     'totalbaru' => $totalBaru,
    //                 //     'dp' => $barangOld->dp,
    //                 //     'nominal_terbayar' => $barangOld->nominal_terbayar,

    //                 // ]);
    //                 if ($totalBaru > ($barangOld->dp_barang + $barangOld->nominal_terbayar)) {
    //                     // Membuat instance dari Request dan mengisi dengan data


    //                     $nominalBaru = $totalBaru - ($barangOld->dp_barang + $barangOld->nominal_terbayar);

    //                     // $data = [
    //                     //     'id_nota' => (string) $barangCheck->id_nota,
    //                     //     'nominal' => (string) $nominalBaru

    //                     // ];


    //                     // // Membuat instance dari UserController
    //                     // $cicilanPiutang = new CicilanPiutangController();

    //                     // // Memanggil metode store dengan objek request yang telah dibuat
    //                     // $cicilanPiutang->storeCicilan($data);


    //                     // return true;


    //                     $nominal = $nominalBaru;
    //                     $id_nota = $barangCheck->id_nota;
    //                     // Buat Bukubesar
    //                     $updateBukuBesar = new BukubesarModel();
    //                     $updateBukuBesar->id_akunbayar = 1;
    //                     $updateBukuBesar->tanggal = date('Y-m-d');
    //                     $updateBukuBesar->kategori =  'barang';
    //                     $updateBukuBesar->keterangan = 'Hutang';

    //                     // $updateBukuBesar->sub_kategori = 'piutang';
    //                     $updateBukuBesar->debit = $nominal; // Masukkan nilai debit yang sesuai
    //                     $updateBukuBesar->kredit = 0; // Jika debit maka kredit harus 0
    //                     $updateBukuBesar->save();
    //                     $riwayatHutang = RiwayatHutangModel::create([
    //                         'id_barang' => $barangCheck->id_barang,
    //                         'id_bukubesar' => $updateBukuBesar->id_bukubesar,
    //                         'nominal_dibayar' =>  $nominal
    //                     ]);


    //                     $barangCheck->nominal_terbayar += $riwayatHutang->nominal_dibayar;


    //                     $barangCheck->save();



    //                     return true;
    //                 }
    //             }
    //             // Lunas ke Hutang

    //         } else if ($barangOld->total > ($barangOld->dp + $barangOld->nominal_terbayar)) {
    //             // dd($barangOld);
    //             $barangCheck = Barang::where('id_barang', $barangOld->id_barang)->first();
    //             $totalBaru = $barangCheck->total;



    //             // Lunas ke Lunas
    //             if ($status_pembayaran == 'lunas') {
    //                 // dd([
    //                 //     'totalbaru' => $totalBaru,
    //                 //     'dp' => $barangOld->dp,
    //                 //     'nominal_terbayar' => $barangOld->nominal_terbayar,

    //                 // ]);
    //                 if ($totalBaru > ($barangOld->dp_barang + $barangOld->nominal_terbayar)) {

    //                     // Membuat instance dari Request dan mengisi dengan data

    //                     $nominalBaru = $totalBaru - ($barangOld->dp_barang + $barangOld->nominal_terbayar);

    //                     // $data = [
    //                     //     'id_nota' => (string) $barangCheck->id_nota,
    //                     //     'nominal' => (string) $nominalBaru

    //                     // ];


    //                     // // Membuat instance dari UserController
    //                     // $cicilanPiutang = new CicilanPiutangController();

    //                     // // Memanggil metode store dengan objek request yang telah dibuat
    //                     // $cicilanPiutang->storeCicilan($data);


    //                     // return true;


    //                     $nominal = $nominalBaru;
    //                     $id_nota = $barangCheck->id_nota;
    //                     // Buat Bukubesar
    //                     $updateBukuBesar = new BukubesarModel();
    //                     $updateBukuBesar->id_akunbayar = 1;
    //                     $updateBukuBesar->tanggal = date('Y-m-d');
    //                     $updateBukuBesar->kategori =  'barang';
    //                     $updateBukuBesar->keterangan = 'PIUTANG';

    //                     // $updateBukuBesar->sub_kategori = 'piutang';
    //                     $updateBukuBesar->debit = $nominal; // Masukkan nilai debit yang sesuai
    //                     $updateBukuBesar->kredit = 0; // Jika debit maka kredit harus 0
    //                     $updateBukuBesar->save();
    //                     $riwayatHutang = RiwayatHutangModel::create([
    //                         'id_barang' => $barangCheck->id_barang,
    //                         'id_bukubesar' => $updateBukuBesar->id_bukubesar,
    //                         'nominal_dibayar' =>  $nominal
    //                     ]);


    //                     $barangCheck->nominal_terbayar += $riwayatHutang->nominal_dibayar;


    //                     $barangCheck->save();



    //                     return true;
    //                 }
    //             }
    //         }
    //     }
    //     return true;
    // }
}
