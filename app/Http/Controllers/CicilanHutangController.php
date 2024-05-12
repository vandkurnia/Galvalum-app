<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarBarangModel;
use App\Models\BukubesarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $barangData = Barang::where('hash_id_barang', $id_barang)->with('bukuBesar')->first();
        // dd($barangData);

        return view('cicilan.hutang.index', compact('barangData'));
    }


    public function edit($id_barang, $id_bukubesar)
    {
        $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();
        if (!$dataBukuBesar) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('cicilan.hutang.edit', compact('dataBukuBesar', 'id_barang'))->render()
        ], 200);
    }
    public function store(Request $request)
    {

        $request->validate([
            'id_barang' => 'required|string|max:10',
            'nominal' => 'required|string|max:255',
        ]);
        $id_barang = $request->get('id_barang');
        $barangData = Barang::where('id_barang', $id_barang)->with('bukuBesar')->first();

        DB::beginTransaction();
        $nominal = $request->get('nominal');
        $updateBukuBesar = new BukubesarModel();
        $updateBukuBesar->id_akunbayar = 1;
        $updateBukuBesar->tanggal = date('Y-m-d');
        $updateBukuBesar->kategori = 'barang';
        $updateBukuBesar->keterangan = '';

        $updateBukuBesar->sub_kategori = 'pelunasan';
        $updateBukuBesar->debit = $nominal; // Masukkan nilai debit yang sesuai
        $updateBukuBesar->kredit = 0; // Jika debit maka kredit harus 0
        $updateBukuBesar->save();
        $bukubesarBarang = BukubesarBarangModel::create([
            'id_barang' => $id_barang,
            'id_bukubesar' => $updateBukuBesar->id_bukubesar
        ]);



        // Membuat Angsuran
        $totalTerbayar = 0;
        $totalAngsuran = 0;
        $barangData2 = Barang::with('bukuBesar')->where('id_barang', $id_barang)->first();
        foreach ($barangData2->bukuBesar as  $barang) {

            $totalTerbayar += $barang->debit;
            $totalAngsuran++;
        }

        $updateBukuBesar2 = BukubesarModel::where('id_bukubesar', $updateBukuBesar->id_bukubesar)->first();
        $updateBukuBesar2->keterangan = 'PELUNASAN HUTANG  BARANG  Ke  ' . $totalAngsuran;
        $updateBukuBesar2->save();



        $barangdata3 = Barang::where('id_barang', $id_barang)->first();
        $barangdata3->nominal_terbayar = $totalTerbayar;
        if ($totalTerbayar > $barangdata3->total) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Barang gagal  karena nominal bayar lebih besar dari total pesanan');
        }
        $barangdata3->save();


        $this->cekLunasAtauHutang($id_barang);


        DB::commit();
        // dump($updateBukuBesar);


        // dd($notaBukubesar);

        return redirect()->route('cicilan.hutang.index', ['id_barang' => $barangdata3->hash_id_barang])->with('success', 'Cicilan piutang berhasil ditambahkan');
    }

    public function update(Request $request, $id_barang, $id_bukubesar)
    {
        $request->validate([
            'nominal' => 'required|string|max:255',
        ]);



        // dd("heheha");



        $barangData = Barang::with('bukuBesar')->where('hash_id_barang', $id_barang)->first();




        DB::beginTransaction();


        $bukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();

        $bukuBesar->debit = $request->get('nominal');
        $bukuBesar->save();

        // Cek total bayar
        $totalTerbayar = 0;
        $barangDataUpdate = Barang::with('bukuBesar')->where('hash_id_barang', $id_barang)->first();
        foreach ($barangDataUpdate->bukuBesar as $dtbarangData) {

            $totalTerbayar += $dtbarangData->debit;
        }



        if ($totalTerbayar > $barangData->total) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Hutang barang gagal diupdate karena nominal bayar lebih besar dari total pesanan');
        }
        $updateBarangPembeli2 = Barang::where('hash_id_barang', $id_barang)->first();
        $updateBarangPembeli2->nominal_terbayar = $totalTerbayar;

        $updateBarangPembeli2->save();


        DB::commit();

        $this->cekLunasAtauHutang($updateBarangPembeli2->id_barang);
        return redirect()->route('cicilan.hutang.index', ['id_barang' => $updateBarangPembeli2->hash_id_barang])->with('success', 'Cicilan hutang berhasil diupdate');
    }

    public function destroy($id_bukubesar, $id_barang)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();
        if ($dataBukuBesar) {
            $dataBukuBesar->delete();


            $barangData = Barang::where('hash_id_barang', $id_barang)->with('bukuBesar')->first();
            $total = 0;
            foreach ($barangData->bukuBesar as $bukuBesar) {
                $total += $bukuBesar->debit;
            }

            DB::beginTransaction();
            $barangData2 = Barang::where('hash_id_barang', $id_barang)->first();
            $barangData2->nominal_terbayar = $total;
            $barangData2->save();

            $this->cekLunasAtauHutang($barangData2->id_barang);
            DB::commit();
            return redirect()->route('cicilan.hutang.index', ['id_barang' => $barangData2->hash_id_barang])->with('success', 'Cicilan piutang dihapus');
        } else {
            $barangData2 = Barang::where('hash_id_barang', $id_barang)->first();
            $this->cekLunasAtauHutang($barangData2->id_barang);
            return redirect()->route('cicilan.hutang.index', ['id_barang' => $barangData2->hash_id_barang])->with('error', 'Cicilan piutang gagal dihapus');
        }
    }
}
