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

    public function storeCicilan($data)
    {
        try {
            $nominal = $data['nominal'];
            $id_nota = $data['id_nota'];

            // Find NotaPembelian
            $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->first();

            if (!$notaPembelian) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Nota not found.'
                ];
            }

            // Create Bukubesar
            $updateBukuBesar = new BukubesarModel();
            $updateBukuBesar->id_akunbayar = 1;
            $updateBukuBesar->tanggal = date('Y-m-d');
            $updateBukuBesar->kategori = 'transaksi';
            $updateBukuBesar->keterangan = 'PIUTANG';
            $updateBukuBesar->debit = $nominal;
            $updateBukuBesar->kredit = 0;
            $updateBukuBesar->save();

            // Create RiwayatPiutang
            $riwayatPiutang = RiwayatPiutangModel::create([
                'id_nota' => $notaPembelian->id_nota,
                'id_bukubesar' => $updateBukuBesar->id_bukubesar,
                'nominal_dibayar' => $nominal
            ]);

            // Update NotaPembelian
            $notaPembelian = NotaPembeli::with('bukuBesar')->where('id_nota', $id_nota)->first();
            $notaPembelian->nominal_terbayar += $riwayatPiutang->nominal_dibayar;

            // Check for completion or overpayment
            if (($notaPembelian->total == ($notaPembelian->dp + $notaPembelian->nominal_terbayar)) ||
                ($notaPembelian->total < ($notaPembelian->dp + $notaPembelian->nominal_terbayar))
            ) {

                // Set completion date
                if (is_null($notaPembelian->tanggal_penyelesaian)) {
                    $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;
                }
            } else {
                if (!is_null($notaPembelian->tanggal_penyelesaian)) {
                    $notaPembelian->tanggal_penyelesaian = null;
                }
            }

            // Check for overpayment
            if (($notaPembelian->nominal_terbayar + $notaPembelian->dp) > $notaPembelian->total) {

                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Nota piutang gagal karena nominal bayar lebih besar dari total pesanan.'
                ];
            }

            // Save updated NotaPembelian
            $notaPembelian->save();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Cicilan berhasil disimpan.'
            ];
        } catch (\Exception $e) {
            // General error handling
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'An error occurred while processing the cicilan: ' . $e->getMessage()
            ];
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'id_nota' => 'required|string|max:10',
            'nominal' => 'required|string|max:255',
        ]);
        $nominal = $request->get('nominal');
        $id_nota = $request->get('id_nota');

        $data = [
            'nominal' => $nominal,
            'id_nota' => $id_nota
        ];

        DB::beginTransaction();


        // Call the storeCicilan method
        $result = $this->storeCicilan($data);
       
        // Check the result and handle accordingly
        if ($result['status'] != 'success') {
            DB::rollBack();


            return redirect()->back()->with($result['status'], $result['message']);
        }
        // $bukuBesar = BukubesarModel::find($notaPembelian->id_bukubesar);
        // $bukuBesar->debit = $notaPembelian->dp;
        // $bukuBesar->save();
        DB::commit();
        // dump($updateBukuBesar);


        // dd($RiwayatPiutangModel);

        return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('success', 'Cicilan piutang berhasil ditambahkan');
    }


    public function updateCicilan($id_piutang, $data)
    {
        try {
            // Find the relevant NotaPembeli
            $notaPembelian = NotaPembeli::where('id_nota', $data['id_nota'])->first();
            if (!$notaPembelian) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Nota not found.'
                ];
            }

            $oldNominaldibayar = $notaPembelian->nominal_terbayar;

            // Find the RiwayatPiutang
            $riwayatPiutang = RiwayatPiutangModel::findOrFail($id_piutang);
            $tambahan = $data['nominal'] - $riwayatPiutang->nominal_dibayar;

            // Update the nominal_dibayar in RiwayatPiutang
            $riwayatPiutang->nominal_dibayar = $data['nominal'];
            $riwayatPiutang->save();

            // Update the corresponding Bukubesar
            $updateBukuBesar = BukubesarModel::find($riwayatPiutang->id_bukubesar);
            if (!$updateBukuBesar) {

                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'BukuBesar not found.'
                ];
            }
            $updateBukuBesar->debit = $riwayatPiutang->nominal_dibayar;
            $updateBukuBesar->save();

            // Update the nominal_terbayar in NotaPembeli
            $notaPembelian->nominal_terbayar += $tambahan;

            // Check for completion or overpayment
            if (($notaPembelian->total == ($notaPembelian->dp + $notaPembelian->nominal_terbayar)) ||
                ($notaPembelian->total < ($notaPembelian->dp + $notaPembelian->nominal_terbayar))
            ) {
                // Set completion date if not already set
                if (is_null($notaPembelian->tanggal_penyelesaian)) {
                    $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;
                }
            } else {
                // Clear completion date if the payment is not completed
                if (!is_null($notaPembelian->tanggal_penyelesaian)) {
                    $notaPembelian->tanggal_penyelesaian = null;
                }
            }

            // Prevent overpayment
            if ($notaPembelian->nominal_terbayar > $notaPembelian->total) {

                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Nota piutang gagal diupdate karena nominal bayar lebih besar dari total pesanan.'
                ];
            }

            // Save the updated NotaPembelian
            $notaPembelian->save();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Cicilan berhasil diupdate.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'An error occurred while updating the cicilan: ' . $e->getMessage()
            ];
        }
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



            $data = [
                'nominal' => $request->input('nominal'),
                'id_nota' => $request->input('id_nota'),
            ];

            $result = $this->updateCicilan($id_piutang, $data);

            // Handle the result
            if ($result['status'] != 'success') {
                DB::rollBack();


                return redirect()->back()->with($result['status'], $result['message']);
            }


            // $bukuBesar = BukubesarModel::find($notaPembelian->id_bukubesar);
            // $bukuBesar->debit = $notaPembelian->dp;
            // $bukuBesar->save();




            DB::commit();
            // $this->cekLunasAtauHutang($id_nota);

            return redirect()->route('cicilan.index', ['id_nota' => $id_nota])->with('success', 'Cicilan piutang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui cicilan piutang: ' . $e->getMessage());
        }
    }




    public function destroyCicilan($id_piutang, $id_nota)
    {
        try {
            // Find the relevant NotaPembeli
            $notaPembelian = NotaPembeli::where('id_nota', $id_nota)->first();

            // If NotaPembeli not found, return an error message
            if (!$notaPembelian) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Nota Pembeli tidak ditemukan'
                ];
            }

            // Find the RiwayatPiutang
            $riwayatPiutang = RiwayatPiutangModel::findOrFail($id_piutang);

            // Update the nominal_terbayar in NotaPembeli
            $notaPembelian->nominal_terbayar -= $riwayatPiutang->nominal_dibayar;

            // Check conditions for updating the tanggal_penyelesaian
            if ($notaPembelian->nominal_terbayar == $notaPembelian->total && is_null($notaPembelian->tanggal_penyelesaian)) {
                $notaPembelian->tanggal_penyelesaian = $notaPembelian->updated_at;
            } elseif ($notaPembelian->nominal_terbayar != $notaPembelian->total && !is_null($notaPembelian->tanggal_penyelesaian)) {
                $notaPembelian->tanggal_penyelesaian = null;
            }
            $notaPembelian->save();

            // Find and delete the corresponding Bukubesar entry
            $bukuBesar = BukubesarModel::find($riwayatPiutang->id_bukubesar);
            if ($bukuBesar) {
                $bukuBesar->delete();
            }

            // Delete the RiwayatPiutang entry
            $riwayatPiutang->delete();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Cicilan berhasil dihapus.'
            ];
        } catch (\Exception $e) {

            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'An error occurred while deleting the cicilan: ' . $e->getMessage()
            ];
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

            $result = $this->destroyCicilan($id_piutang, $id_nota);

            // Handle the result
            if ($result['status'] != 'success') {
                DB::rollBack();


                return redirect()->back()->with($result['status'], $result['message']);
            }



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
