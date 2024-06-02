<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarBarangModel;
use App\Models\BukubesarModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\PemasokBarang;
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
            $totalStok = $barang->stokBarang->sum('stok_masuk') - $barang->stokBarang->sum('stok_keluar');
            $barang->stok = $totalStok;
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
            $stokbarang = StokBarangModel::where('id_barang', $dataBarang->id_barang)
                ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
                ->whereNull('deleted_at')
                ->first();

            // Menambahkan jumlah stok ke dalam data barang
            $dataBarang->stok = $stokbarang->stok;
            $stokbarang = StokBarangModel::where('id_barang', $dataBarang->id_barang)->first();
            $dataBarang->stokoriginal = $stokbarang->stok_masuk;
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
        $barang->nominal_terbayar =  $request->get('nominal_terbayar');
        $barang->tenggat_bayar = $request->get('tenggat_bayar');
        $barang->save();



        // Buat record baru untuk BukuBesar
        $bukuBesar = new BukubesarModel();

        $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        $bukuBesar->debit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        $bukuBesar->save();

        $stokBarang = StokBarangModel::create([
            'stok_masuk' => $request->stok,
            'id_barang' => $barang->id_barang,
            'tipe_stok' => 'stokbarang'
        ]);





        $bukubesarBarang = new BukubesarBarangModel();
        $bukubesarBarang->id_barang = $barang->id_barang;
        $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;

        $bukubesarBarang->save();



        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = $stokBarang; // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_create';
        $logStokBarang->keterangan = 'Tambah barang ke stok dengan total stok awal ' . $stokBarang->stok_masuk;
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->save();
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data  Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            // 'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            // 'id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            'stok' => 'required|min:0',
            'harga_barang' => 'required|numeric|min:0',
            'harga_barang_pemasok' => 'required|numeric|min:0',
            'status_pembelian' => 'required|in:lunas,hutang',
            'nominal_terbayar' => 'nullable|numeric|min:0',
            'tenggat_bayar' => 'nullable|date',

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
        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->ukuran = $request->ukuran;
        $barang->id_tipe_barang = $request->id_tipe_barang;
        $barang->harga_barang = $request->harga_barang;
        $barang->harga_barang_pemasok = $request->harga_barang_pemasok;
        $total_lama = $barang->total;
        $nominal_terbayar_lama =  $barang->nominal_terbayar;
        $barang->nominal_terbayar = $request->nominal_terbayar;
        $barang->tenggat_bayar = $request->tenggat_bayar;
        $barang->save();


        // dd([
        //     'nominal_terbayar_lama' => $nominal_terbayar_lama,
        //     'nominal_terbayar_baru' => $request->nominal_terbayar,
        //     'total' => $total_lama
        // ]);




        // Menghitung total stok
        $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
            ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
            ->first();

        // Stok Lama 
        $stokLama = $stokBarang->stok;



        $stok = $request->stok;

        if ($stok != $stokBarang->stok) {


            $selisihStokReqdanAsli = $stok  - $stokBarang->stok;

            // Proses pembaruan stok barang
            $stok_barang = DB::table('stok_barang')
                ->where('id_barang', $barang->id_barang)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if ($stok_barang) {
                // Mendapatkan waktu sekarang
                $now = Carbon::now();



                // Proses pembaruan stok barang
                $stokupdate = $stok_barang->stok_masuk + $selisihStokReqdanAsli;
                // dd([
                //     'stokmasuk' => $stok_barang->stok_masuk,
                //     'selisih' => $selisihStokReqdanAsli,
                //     'stok_update' => $stokupdate
                // ]);
                $stokBarangubahStok = StokBarangModel::find($stok_barang->id);
                // dd($stokBarangubahStok);
                $stokBarangubahStok->stok_masuk = $stokupdate;

                $stokBarangubahStok->save();


                $stokBaru = $stokBarangubahStok->stok_masuk;
                // Stok Baru




                // Update total barang setelah mengubah stok masuk
                $updatebarangtotal = Barang::find($barang->id_barang);
                $updatebarangtotal->total = $stokBarangubahStok->stok_masuk * $updatebarangtotal->harga_barang_pemasok;
                $updatebarangtotal->save();





                // Simpan ke log
                $logStokBarang = new LogStokBarangModel();
                $logStokBarang->json_content = $stokBarangubahStok; // Sesuaikan dengan isi json_content Anda
                $logStokBarang->tipe_log = 'barang_update';
                $logStokBarang->keterangan = 'Update barang dari ' . $stokLama . ' ke ' . $stokBaru;
                $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                $logStokBarang->id_stok_barang = $stokBarangubahStok->id; // Sesuaikan dengan id_stok_barang yang ada
                $logStokBarang->id_barang = $stokBarangubahStok->id_barang; // Sesuaikan dengan id_barang yang ada
                $logStokBarang->save();
            }
        }


        // Menggunakan DB::transaction untuk menjaga integritas transaksi
        // $bukubesarBarang = $barang->bukuBesar;
        // dd([
        //     'nominal_terbayar' => $barang->nominal_terbayar,
        //     'request nominal terbayar' => $request->nominal_terbayar,
        //     'status' => $nominal_terbayar_lama != $request->nominal_terbayar,
        //     'nominal_terbayar_lama' =>  $nominal_terbayar_lama
        // ]);

        // dd(['test' => $total_lama == $nominal_terbayar_lama]);

        // Menghitung lagi untuk konversi status pembayaran
        if ($total_lama == $nominal_terbayar_lama) {
            $barangTerbaru =  Barang::find($barang->id_barang);
            $total_baru = $barangTerbaru->total;
            $nominal_terbayar_baru = $barangTerbaru->nominal_terbayar;
            // Lunas ke lunas
            if ($total_baru == $nominal_terbayar_baru) {




                $bukubesarbarang = BukubesarBarangModel::where('id_barang', $barang->id_barang)->first();
                $bukubesar = BukubesarModel::find($bukubesarbarang->id_bukubesar);
                $stokBarangpertama = StokBarangModel::where('id_barang', $barang->id_barang)->first();

                // Selisih antara debit pertama dengan 
                $bukubesar->debit = $barangTerbaru->harga_pemasok * $stokBarangpertama->stok_masuk;
                $bukubesar->save();
            }

            // Lunas ke hutang
            else {



                $barangTerbaru =  Barang::find($barang->id_barang);
                $bukubesarbarang = BukubesarBarangModel::where('id_barang', $barang->id_barang)->first();
                $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                // Selisih antara debit pertama dengan 
                $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                $bukubesarUpdate->save();

                // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                $bukubesarBarangs = BukubesarBarangModel::where('id_barang', $barang->id_barang)
                    ->skip(1) // Lewatkan entri pertama
                    ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                    ->get();

                // Hapus semua entri buku besar setelah yang pertama
                foreach ($bukubesarBarangs as $bukubesarBarang) {
                    $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                    if ($bukubesarToDelete) {
                        $bukubesarToDelete->forceDelete();
                    }
                }
            }
        } else {

            $cekTotal =  Barang::find($barang->id_barang);
            $total_baru = $cekTotal->total;
            $nominal_terbayar_baru = $cekTotal->nominal_terbayar;
            // Hutang ke lunas 
            if ($total_baru == $nominal_terbayar_baru) {

                $barangTerbaru =  Barang::find($barang->id_barang);
                $bukubesarbarang = BukubesarBarangModel::where('id_barang', $barang->id_barang)->first();
                $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                // Selisih antara debit pertama dengan 
                $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                $bukubesarUpdate->save();


                // $bukuBesarIkut = BukubesarModel::find($bukubesarUpdate->id_bukubesar);


                // dd([
                //     'barang' => $bukubesarUpdate,
                //     'kucing' => $barangTerbaru,
                //     'test' => $bukuBesarIkut
                // ]);


                // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                $bukubesarBarangs = BukubesarBarangModel::where('id_barang', $barang->id_barang)
                    ->skip(1) // Lewatkan entri pertama
                    ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                    ->get();

                // Hapus semua entri buku besar setelah yang pertama
                foreach ($bukubesarBarangs as $bukubesarBarang) {
                    $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                    if ($bukubesarToDelete) {
                        $bukubesarToDelete->forceDelete();
                    }
                }
            } else if ($total_lama != $total_baru || $nominal_terbayar_lama != $request->nominal_terbayar) {
                $barangTerbaru =  Barang::find($barang->id_barang);
                $bukubesarbarang = BukubesarBarangModel::where('id_barang', $barang->id_barang)->first();
                $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                // Selisih antara debit pertama dengan 
                $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                $bukubesarUpdate->save();


                // $bukuBesarIkut = BukubesarModel::find($bukubesarUpdate->id_bukubesar);


                // dd([
                //     'barang' => $bukubesarUpdate,
                //     'kucing' => $barangTerbaru,
                //     'test' => $bukuBesarIkut
                // ]);


                // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                $bukubesarBarangs = BukubesarBarangModel::where('id_barang', $barang->id_barang)
                    ->skip(1) // Lewatkan entri pertama
                    ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                    ->get();

                // Hapus semua entri buku besar setelah yang pertama
                foreach ($bukubesarBarangs as $bukubesarBarang) {
                    $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                    if ($bukubesarToDelete) {
                        $bukubesarToDelete->forceDelete();
                    }
                }
            }
        }

        // dd("atas");
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data barang berhasil diperbarui');
    }


    public function destroy($id)
    {
        $dataBarang = Barang::with('bukuBesar')->where('hash_id_barang', $id)->first();
        if ($dataBarang) {

            // hapus bukubesar
            foreach ($dataBarang->bukuBesar as $bukubesar) {
                $bukuBesar = BukubesarModel::find($bukubesar->id_bukubesar);
                $bukuBesar->delete();
            }
            $dataBarang->delete();

            return redirect()->route('stok.index')->with('success', 'Barang berhasil dihapus');
        } else {
            return redirect()->route('stok.index')->with('error', 'Barang gagal dihapus');
        }
    }


    public function showStokBarang($id)
    {

        $dataBarang = Barang::with('stokBarang')->where('hash_id_barang', $id)->first();

        if (!$dataBarang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }

        $dataBarangBaru = [];
        $totalStok = $dataBarang->stokBarang->sum('stok_masuk') - $dataBarang->stokBarang->sum('stok_keluar');
        $dataBarang->stok = $totalStok;
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
            'nominal_terbayar' => 'required',

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
        $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)->first();


        // Jumlah Stok Final Stok dari input
        $stoktambah = $validatedData['stok_tambah'] +  $stokBarang->stok_masuk;
        $stokBarang->stok_masuk = $stoktambah;
        $stokBarang->save();




        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = $stokBarang; // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_tambah_stok';
        $logStokBarang->keterangan = 'Tambah stok barang sebanyak ' . $validatedData['stok_tambah'];
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $stokBarang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->save();




        // Update total
        $barang->total = $stokBarang->stok_masuk * $barang->harga_barang_pemasok;

        $barang->save();



        // Buat record baru untuk BukuBesar
        $bukuBesar = new BukubesarModel();

        $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        $bukuBesar->kategori = "barang_tambah_stok"; // Isi dengan kategori yang sesuai
        $bukuBesar->keterangan = 'TAMBAH STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        $bukuBesar->debit = $request->nominal_terbayar; // Isi dengan nilai kredit yang sesuai
        $bukuBesar->save();

        $bukubesarBarang = new BukubesarBarangModel();
        $bukubesarBarang->id_barang = $barang->id_barang;
        $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;
        $bukubesarBarang->save();

        // Hitung lagi nominal terbayar stok 
        $barangupdated = Barang::with('bukuBesar')->find($barang->id_barang);
        $totalNominalTerbayar = 0;
        foreach ($barangupdated->bukuBesar as $bukuBesar) {
            $totalNominalTerbayar += $bukuBesar->debit;
        }

        $barangupdated->nominal_terbayar = $totalNominalTerbayar;
        $barangupdated->save();


        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }
    // public function minusStok(Request $request)
    // {


    //     $validatedData = $request->validate([
    //         'stok_kurang' => 'required|numeric|min:0',
    //         'id_barang' => 'required|exists:barangs,hash_id_barang',
    //     ], [
    //         'stok_kurang.required' => 'Pengurangan Stok harus diisi.',
    //         'stok_kurang.numeric' => 'Pengurangan Stok harus berupa angka.',
    //         'stok_kurang.min' => 'Pengurangan Stok harus lebih dari atau sama dengan 0.',
    //         'id_barang.exists' => 'Barang tidak ditemukan.',
    //     ]);

    //     $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

    //     if (!$barang) {
    //         return redirect()->back()->with('error', 'Barang tidak ditemukan.');
    //     }

    //     if ($validatedData['stok_kurang'] <= 0) {
    //         return redirect()->back()->with('error', 'Pengurangan tidak valid atau 0.');
    //     }


    //     DB::beginTransaction();
    //     // $bukuBesar = new BukubesarModel();
    //     // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
    //     // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->hash_id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
    //     // $bukuBesar->tanggal = date('Y-m-d');
    //     // $bukuBesar->sub_kategori = "hutang";
    //     // $bukuBesar->debit = $validatedData['stok_kurang'];
    //     // $bukuBesar->keterangan = "Pengurangan stok " . $validatedData['stok_kurang'];
    //     // $bukuBesar->save();


    //     StokBarangModel::create([
    //         'stok_keluar' => $validatedData['stok_kurang'],
    //         'id_barang' => $barang->id_barang
    //         // 'id_bukubesar' => $bukuBesar->id_bukubesar
    //     ]);



    //     // Mencari barang berdasarkan hash_id_barang
    //     $barang = Barang::find($barang->id_barang);

    //     // Kembalikan jika barang tidak ada
    //     if (!$barang) {
    //         return redirect()->back()->with('error', 'Barang tidak ada');
    //     }
    //     // Menghitung total stok
    //     $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
    //         ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
    //         ->first();

    //     // Update total
    //     $barang->total = $stokBarang->stok * $barang->harga_barang_pemasok;

    //     $barang->save();
    //     DB::commit();

    //     return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    // }
}
