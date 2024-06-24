<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\RiwayatHutangModel;
use App\Models\BukubesarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CicilanHutangController extends Controller
{
    private function cekLunasAtauHutang($id_barang)
    {
        // $barang = Barang::with('bukuBesar')->where('id_barang',  $id_barang)->first();
        // if ($barang->total == $barang->nominal_terbayar) {
        //     $barang->status_pembayaran = "lunas";
        // } else {
        //     $barang->status_pembayaran = "hutang";
        // }
        // $barang->save();
    }
    public function index($id_barang)
    {

        // return redirect()->back();
        $barangData = Barang::where('hash_id_barang', $id_barang)->with('riwayatHutang')->first();

        // dd($barangData);

        return view('cicilan.hutang.index', compact('barangData'));
    }


    public function edit($id_barang, $id_riwayathutang)
    {
        // $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();
        // if (!$dataBukuBesar) {
        //     return response()->json([
        //         'code' => 404,
        //         'message' => 'Not found',
        //         'data' => null
        //     ], 404);
        // }


        // return response()->json([
        //     'code' => 200,
        //     'message' => 'Success',
        //     'data' => view('cicilan.hutang.edit', compact('dataBukuBesar', 'id_barang'))->render()
        // ], 200);
        $dataRiwayatHutang = RiwayatHutangModel::find($id_riwayathutang);
        if (!$dataRiwayatHutang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('cicilan.hutang.edit', compact('dataRiwayatHutang', 'id_barang'))->render()
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|string|max:10',
            'nominal' => 'required|string|max:255',
        ]);

        $id_barang = $request->get('id_barang');
        $nominal = $request->get('nominal');

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Cek apakah barang ada
            // $barangData = Barang::where('id_barang', $id_barang)->with('bukubesar')->first();
            $barangData = Barang::where('id_barang', $id_barang)->first();
            if (!$barangData) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Barang tidak ada');
            }

            // New
            // Cek apakah total terbayar lebih dari total harga barang

            $barangData->nominal_terbayar = $barangData->nominal_terbayar + $nominal;
            if ($barangData->nominal_terbayar > $barangData->total) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Barang gagal karena nominal bayar lebih besar dari total pesanan');
            }
            $barangData->save();
            $riwayatHutangData = new RiwayatHutangModel();
            $riwayatHutangData->id_barang = $barangData->id_barang;
            $riwayatHutangData->nominal_dibayar = $nominal;
            $riwayatHutangData->save();

            // End New

            // Buat entri baru di buku besar
            // $updateBukuBesar = new BukubesarModel();
            // $updateBukuBesar->id_akunbayar = 1;
            // $updateBukuBesar->tanggal = date('Y-m-d');
            // $updateBukuBesar->kategori = 'barang';
            // $updateBukuBesar->keterangan = 'Pelunasan Hutang';
            // $updateBukuBesar->debit = $nominal;
            // $updateBukuBesar->kredit = 0;
            // $updateBukuBesar->save();

            // // Buat entri di tabel pivot barang_bukubesar
            // $barangData->bukubesar()->attach($updateBukuBesar->id_bukubesar);

            // Menghitung total terbayar dan total angsuran
            // $totalTerbayar = 0;
            // $totalAngsuran = 0;

            // $barangData2 = Barang::with('bukubesar')->where('id_barang', $id_barang)->first();
            // foreach ($barangData2->bukubesar as $barang) {
            //     $totalTerbayar += $barang->debit - $barang->kredit;
            //     $totalAngsuran++;
            // }

            // // Update keterangan buku besar dengan nomor angsuran
            // $updateBukuBesar2 = BukubesarModel::where('id_bukubesar', $updateBukuBesar->id_bukubesar)->first();
            // $updateBukuBesar2->keterangan = 'PELUNASAN HUTANG BARANG Ke ' . $totalAngsuran;
            // $updateBukuBesar2->save();

            // Update nominal_terbayar pada barang
            // $barangdata3 = Barang::where('id_barang', $id_barang)->first();
            // $barangdata3->nominal_terbayar = $totalTerbayar;


            // Cek apakah total terbayar lebih dari total harga barang
            // if ($totalTerbayar > $barangdata3->total) {
            //     DB::rollBack();
            //     return redirect()->back()->with('error', 'Barang gagal karena nominal bayar lebih besar dari total pesanan');
            // }
            // $barangdata3->save();





            // Commit transaksi
            DB::commit();

            return redirect()->route('cicilan.hutang.index', ['id_barang' => $barangData->hash_id_barang])->with('success', 'Cicilan piutang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e->getMessage());
            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }


    public function update(Request $request, $id_barang, $id_riwayathutang)
    {
        $request->validate([
            'nominal' => 'required|numeric|max:9999999999.99', // Validasi nominal sebagai angka
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // New

            $barangData = Barang::where('hash_id_barang', $id_barang)->first();
            if (!$barangData) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Barang tidak ada');
            }

            $riwayatHutang = RiwayatHutangModel::find($id_riwayathutang);
            // $riwayatHutang->save();

            // Perbedaan  baru dan lama
            $perbedaan =   $request->nominal - $riwayatHutang->nominal_dibayar;
            $riwayatHutang->nominal_dibayar = $request->nominal;
            $riwayatHutang->save();


            // Cek apakah total terbayar lebih dari total harga barang

            $barangData->nominal_terbayar = $barangData->nominal_terbayar + $perbedaan;
            if ($barangData->nominal_terbayar > $barangData->total) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Barang gagal karena nominal bayar lebih besar dari total pesanan');
            }

            $barangData->save();

            // $riwayatHutangData = new RiwayatHutangModel();
            // $riwayatHutangData->id_barang = $barangData->id_barang;
            // $riwayatHutangData->nominal_dibayar = $nominal;
            // $riwayatHutangData->save();



            // End New






            // Cek apakah barang ada
            // $barangData = Barang::with('bukubesar')->where('hash_id_barang', $id_barang)->first();
            // if (!$barangData) {
            //     DB::rollBack();
            //     return redirect()->back()->with('error', 'Barang tidak ada');
            // }

            // // Cek apakah entri buku besar ada
            // $bukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();
            // if (!$bukuBesar) {
            //     DB::rollBack();
            //     return redirect()->back()->with('error', 'Entri buku besar tidak ada');
            // }

            // // Update nilai debit dan kredit
            // $bukuBesar->debit = $request->get('nominal');
            // $bukuBesar->kredit = 0; // Asumsi jika debit maka kredit di-set ke 0
            // $bukuBesar->save();

            // // Hitung ulang total terbayar
            // $totalTerbayar = 0;
            // $barangDataUpdate = Barang::with('bukubesar')->where('hash_id_barang', $id_barang)->first();
            // foreach ($barangDataUpdate->bukubesar as $dtbarangData) {
            //     $totalTerbayar += $dtbarangData->debit - $dtbarangData->kredit;
            // }

            // // Cek apakah total terbayar lebih dari total harga barang
            // if ($totalTerbayar > $barangData->total) {
            //     DB::rollBack();
            //     return redirect()->back()->with('error', 'Hutang barang gagal diupdate karena nominal bayar lebih besar dari total pesanan');
            // }

            // // Update nominal_terbayar pada barang
            // $updateBarangPembeli2 = Barang::where('hash_id_barang', $id_barang)->first();
            // $updateBarangPembeli2->nominal_terbayar = $totalTerbayar;
            // $updateBarangPembeli2->save();

            // Commit transaksi
            DB::commit();

            // Cek status lunas atau hutang
            // $this->cekLunasAtauHutang($updateBarangPembeli2->id_barang);

            return redirect()->route('cicilan.hutang.index', ['id_barang' => $barangData->hash_id_barang])->with('success', 'Cicilan hutang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the exception
            Log::error('Error occurred: ' . $e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }


    public function destroy($id_bukubesar, $id_barang)
    {
        DB::beginTransaction();
        try {

            $barangData = Barang::where('hash_id_barang', $id_barang)->first();

            // Check if $barangData exists
            if (!$barangData) {
                throw new \Exception('Barang data not found.');
            }
            // Cari Riwayat Hutang;
            $riwayatHutang = RiwayatHutangModel::find($id_bukubesar);


            // Check if $riwayatHutang exists
            if (!$riwayatHutang) {
                throw new \Exception('Riwayat hutang not found.');
            }
            // Ubah nominal_terbayar
            $barangData->nominal_terbayar -= $riwayatHutang->nominal_dibayar;

            $barangData->save();


            // Hapus riwayat hutang
            $riwayatHutang->delete();

            // Cek apakah entri buku besar ada
            // $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();
            // if (!$dataBukuBesar) {
            //     DB::rollBack();
            //     return redirect()->route('cicilan.hutang.index', ['id_barang' => $id_barang])->with('error', 'Entri buku besar tidak ditemukan');
            // }

            // // Ambil data barang beserta relasi buku besarnya
            // $barangData = Barang::where('hash_id_barang', $id_barang)->with('bukubesar')->first();
            // if (!$barangData) {
            //     DB::rollBack();
            //     return redirect()->route('cicilan.hutang.index', ['id_barang' => $id_barang])->with('error', 'Barang tidak ditemukan');
            // }

            // // Hitung ulang nominal_terbayar sebelum penghapusan
            // $totalTerbayar = $barangData->bukubesar->sum('debit') - $barangData->bukubesar->sum('kredit');

            // // Hapus entri dari tabel pivot
            // RiwayatHutangModel::where('id_bukubesar', $dataBukuBesar->id_bukubesar)->delete();

            // // Hapus entri buku besar
            // $dataBukuBesar->delete();

            // // Hitung ulang nominal_terbayar setelah penghapusan
            // $totalTerbayar -= $dataBukuBesar->debit;

            // // Update nominal_terbayar pada barang
            // $barangData->nominal_terbayar = $totalTerbayar;
            // $barangData->save();


            DB::commit();
            return redirect()->route('cicilan.hutang.index', ['id_barang' => $barangData->hash_id_barang])->with('success', 'Cicilan hutang berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the exception
            Log::error('Error occurred: ' . $e->getMessage(), ['exception' => $e]);

            // Fix syntax error and choose the appropriate redirect method
            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage());
            // If you prefer redirecting to a specific route, uncomment the line below
            // return redirect()->route('cicilan.hutang.index', ['id_barang' => $id_barang])->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }
}
