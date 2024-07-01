<?php

namespace App\Http\Controllers;

use App\Models\BukubesarModel;
use App\Models\RiwayatPiutangModel;
use App\Models\NotaPembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CicilanPiutangController extends Controller
{

    // private function cekLunasAtauHutang($id_nota)
    // {
    //     $notaPembelian2 = NotaPembeli::with('bukuBesar')->where('id_nota',  $id_nota)->first();
    //     if ($notaPembelian2->total == $notaPembelian2->nominal_terbayar) {
    //         $notaPembelian2->status_pembayaran = "lunas";
    //     } else {
    //         $notaPembelian2->status_pembayaran = "hutang";
    //     }
    //     $notaPembelian2->save();
    // }
    public function index($id_nota)
    {

        $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->with('Piutang')->first();

        // Periksa kondisi untuk tanggal penyelesaian
        if (($notaPembelian->nominal_terbayar + $notaPembelian->dp) == $notaPembelian->total && is_null($notaPembelian->tanggal_penyelesaian)) {
            $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;  // Atau $notaPembelian->updated_at jika diperlukan
            $notaPembelian->save();
        } elseif (($notaPembelian->nominal_terbayar + $notaPembelian->dp) != $notaPembelian->total && !is_null($notaPembelian->tanggal_penyelesaian)) {
            $notaPembelian->tanggal_penyelesaian = null;
            $notaPembelian->save();
        }

        return view('cicilan.piutang.index', compact('notaPembelian'));
    }


    public function edit($id_nota, $id_bukubesar)
    {


        // $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id_bukubesar)->first();
        $riwayatPiutang = RiwayatPiutangModel::find($id_bukubesar);
        if (!$riwayatPiutang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('cicilan.piutang.edit', compact('riwayatPiutang', 'id_nota'))->render()
        ], 200);
    }
    public function store(Request $request)
    {

        $request->validate([
            'id_nota' => 'required|string|max:10',
            'nominal' => 'required|string|max:255',
        ]);
        $id_nota = $request->get('id_nota');
        $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->first();

        DB::beginTransaction();
        $nominal = $request->get('nominal');
        // $updateBukuBesar = new BukubesarModel();
        // $updateBukuBesar->id_akunbayar = 1;
        // $updateBukuBesar->tanggal = date('Y-m-d');
        // $updateBukuBesar->kategori = 'transaksi';
        // $updateBukuBesar->keterangan = '';

        // // $updateBukuBesar->sub_kategori = 'piutang';
        // $updateBukuBesar->debit = $nominal; // Masukkan nilai debit yang sesuai
        // $updateBukuBesar->kredit = 0; // Jika debit maka kredit harus 0
        // $updateBukuBesar->save();
        $riwayatPiutang = RiwayatPiutangModel::create([
            'id_nota' => $notaPembelian->id_nota,
            // 'id_bukubesar' => $updateBukuBesar->id_bukubesar,
            'nominal_dibayar' =>  $nominal
        ]);
        // $totalTerbayar = 0;
        // $totalAngsuran = 0;
        // $notaPembeli = NotaPembeli::with('bukuBesar')->where('id_nota', $id_nota)->first();

        // foreach ($notaPembeli->bukuBesar as  $dtNotaPembeli) {

        //     $totalTerbayar += $dtNotaPembeli->debit;
        //     $totalAngsuran++;
        // }

        // $updateBukuBesar2 = BukubesarModel::where('id_bukubesar', $updateBukuBesar->id_bukubesar)->first();
        // $updateBukuBesar2->keterangan = 'PELUNASAN PIUTANG  NOTA ' . $notaPembelian->no_nota . " Ke " . $totalAngsuran;
        // $updateBukuBesar2->save();



        $notaPembelian = NotaPembeli::with('bukuBesar')->where('id_nota', $id_nota)->first();
        $notaPembelian->nominal_terbayar += $riwayatPiutang->nominal_dibayar;



        // Periksa kondisi untuk tanggal penyelesaian
        if ($notaPembelian->nominal_terbayar == $notaPembelian->total && is_null($notaPembelian->tanggal_penyelesaian)) {
            $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;  // Atau $notaPembelian->updated_at jika diperlukan
        } elseif ($notaPembelian->nominal_terbayar != $notaPembelian->total && !is_null($notaPembelian->tanggal_penyelesaian)) {
            $notaPembelian->tanggal_penyelesaian = null;
        }

        if (($notaPembelian->nominal_terbayar + $notaPembelian->dp) > $notaPembelian->total) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Nota piutang gagal  karena nominal bayar lebih besar dari total pesanan');
        }
        $notaPembelian->save();



        $bukuBesar = BukubesarModel::find($notaPembelian->id_bukubesar);
        $bukuBesar->debit = $notaPembelian->nominal_terbayar;
        $bukuBesar->save();





        DB::commit();
        // dump($updateBukuBesar);


        // dd($RiwayatPiutangModel);

        return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('success', 'Cicilan piutang berhasil ditambahkan');
    }

    public function update(Request $request, $id_nota, $id_piutang)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Ambil data Bukubesar yang akan diupdate
            // $bukuBesar = BukubesarModel::findOrFail($id_bukubesar);
            // $bukuBesar->debit = $request->get('nominal');
            // $bukuBesar->save();

            // Ambil semua entri buku besar yang terkait dengan nota
            $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->first();
            $oldNominaldibayar = $notaPembelian->nominal_terbayar;



            $riwayatPiutang = RiwayatPiutangModel::findOrFail($id_piutang);

            $tambahan =   $request->nominal - $riwayatPiutang->nominal_dibayar;
            $riwayatPiutang->nominal_dibayar =  $request->nominal;
            $riwayatPiutang->save();
            // Hitung total terbayar
            // $totalTerbayar = $notaPembelian->bukuBesar->sum('debit');


            $notaPembelian->nominal_terbayar += $tambahan;

            // dd([
            //     'sebelum' => $oldNominaldibayar,
            //     'perbedaan' => $tambahan,
            //     'sesudah' =>  $notaPembelian->nominal_terbayar

            // ]);

            // Periksa kondisi untuk tanggal penyelesaian
            if ($notaPembelian->nominal_terbayar == $notaPembelian->total && is_null($notaPembelian->tanggal_penyelesaian)) {
                $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;  // Atau $notaPembelian->updated_at jika diperlukan
            } elseif ($notaPembelian->nominal_terbayar != $notaPembelian->total && !is_null($notaPembelian->tanggal_penyelesaian)) {
                $notaPembelian->tanggal_penyelesaian = null;
            }
            if ($notaPembelian->nominal_terbayar > $notaPembelian->total) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Nota piutang gagal diupdate karena nominal bayar lebih besar dari total pesanan');
            }

            // $notaPembelian->nominal_terbayar = $totalTerbayar;
            $notaPembelian->save();




            $bukuBesar = BukubesarModel::find($notaPembelian->id_bukubesar);
            $bukuBesar->debit = $notaPembelian->nominal_terbayar;
            $bukuBesar->save();




            DB::commit();
            // $this->cekLunasAtauHutang($id_nota);

            return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('success', 'Cicilan piutang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui cicilan piutang: ' . $e->getMessage());
        }
    }


    public function destroy($id_piutang, $id_nota)
    {
        DB::beginTransaction();

        try {
            // Cari data Bukubesar yang akan dihapus
            // $dataBukuBesar = BukubesarModel::where('id_bukubesar', $id_bukubesar)->first();

            // // Jika data tidak ditemukan, kembalikan dengan pesan error
            // if (!$dataBukuBesar) {
            //     return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('error', 'Data Bukubesar tidak ditemukan');
            // }

            // // Hapus data Bukubesar
            // $dataBukuBesar->delete();

            // Cari nota pembelian dan hitung ulang total terbayar
            // $notaPembelian = NotaPembeli::with('bukuBesar')->where('id_nota', $id_nota)->first();
            $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->first();



            // Jika nota pembelian tidak ditemukan, kembalikan dengan pesan error
            if (!$notaPembelian) {
                DB::rollBack();
                return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('error', 'Nota Pembeli tidak ditemukan');
            }

            // Hitung ulang total terbayar
            // $totalTerbayar = $notaPembelian->bukuBesar->sum('debit');


            $riwayatPiutang = RiwayatPiutangModel::findOrFail($id_piutang);


            // Perbarui nominal_terbayar pada nota pembeli
            $notaPembelian->nominal_terbayar -= $riwayatPiutang->nominal_dibayar;



            // Periksa kondisi untuk tanggal penyelesaian
            if ($notaPembelian->nominal_terbayar == $notaPembelian->total && is_null($notaPembelian->tanggal_penyelesaian)) {
                $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;  // Atau $notaPembelian->updated_at jika diperlukan
            } elseif ($notaPembelian->nominal_terbayar != $notaPembelian->total && !is_null($notaPembelian->tanggal_penyelesaian)) {
                $notaPembelian->tanggal_penyelesaian = null;
            }
            $notaPembelian->save();

            $riwayatPiutang->delete();




            $bukuBesar = BukubesarModel::find($notaPembelian->id_bukubesar);
            $bukuBesar->debit = $notaPembelian->nominal_terbayar;
            $bukuBesar->save();


            // Periksa status lunas atau hutang
            // $this->cekLunasAtauHutang($id_nota);

            DB::commit();
            return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('success', 'Cicilan piutang berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('error', 'Terjadi kesalahan saat menghapus cicilan piutang: ' . $e->getMessage());
        }
    }

    public function notVisible($id_nota)
    {
        // Cari nota pembeli berdasarkan id_nota
        $notaPembeli = NotaPembeli::find($id_nota);

        if ($notaPembeli) {
            // Update piutang_is_visible menjadi 'no'
            $notaPembeli->piutang_is_visible = 'no';
            $notaPembeli->save();

            return redirect()->back()->with('success', 'Cicilan berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Nota tidak ditemukan.');
        }
    }
}
