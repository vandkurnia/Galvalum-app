<?php

namespace App\Http\Controllers\JsonType;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DiskonModel;
use App\Models\NotaPembeli;
use App\Models\PesananPembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PemesananBarangJsonController extends Controller
{
    private function notaPembelianUpdate($notaPembeli)
    {
        $subTotal = 0;
        $totalDiskon = 0;
        $updatedPesananPembeli = NotaPembeli::where('id_nota', $notaPembeli->id_nota)->with('PesananPembeli')->first();
        foreach ($updatedPesananPembeli->PesananPembeli as $pesananPembeli) {
            $subTotal += $pesananPembeli->harga *  $pesananPembeli->jumlah_pembelian;
            $totalDiskon += $pesananPembeli->diskon;
        }
        $updateNotaPembeli = NotaPembeli::find($notaPembeli->id_nota);
        $updateNotaPembeli->sub_total = $subTotal;
        $updateNotaPembeli->diskon = $totalDiskon;

        $updateNotaPembeli->total = ($updateNotaPembeli->sub_total  - $updateNotaPembeli->diskon) - $updateNotaPembeli->pajak;
        $updateNotaPembeli->save();
    }
    public function hapusPesanan(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'no_nota' => 'required|exists:nota_pembelis,no_nota',
            'id_pesanan' => 'required|exists:pesanan_pembelis,id_pesanan',
        ]);
        // Cek validasi
        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
        }

        $no_nota = $request->input('no_nota');
        $id_pesanan = $request->input('id_pesanan');

        // Meload data NotaPembeli berdasarkan no_nota
        $notaPembeli = NotaPembeli::where('no_nota', $no_nota)->with('PesananPembeli')->first();

        // Memeriksa apakah NotaPembeli ditemukan
        if ($notaPembeli) {
            // Menghapus setiap PesananPembeli dan mengembalikan stok barang
            $pesananPembeli = PesananPembeli::where('id_nota', $notaPembeli->id_nota)->where('id_pesanan', $id_pesanan)->first();
            $barangData = Barang::where('id_barang', $pesananPembeli->id_barang)->lockForUpdate()->first();

            $barangData->stok += $barangData->total_pesanan;
            $barangData->save();

            // Menghapus PesananPembeli
            $pesananPembeli->forceDelete();
            $this->notaPembelianUpdate($notaPembeli);

            // Mengembalikan respons sukses
            return response()->json(['message' => 'Pesanan berhasil dihapus']);
        } else {
            // NotaPembeli tidak ditemukan, kembalikan pesan error
            return response()->json(['message' => 'NotaPembeli tidak ditemukan'], 404);
        }
    }

    public function updatePesanan(Request $request)
    {

        // Validasi input
        $validator = Validator::make($request->all(), [
            'no_nota' => 'required|exists:nota_pembelis,no_nota',
            'id_pesanan' => 'required',
            'pesanan' => 'required'
        ]);

        // Cek validasi
        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid', 'errors' => $validator->errors()], 422);
        }

        $no_nota = $request->input('no_nota');
        $id_pesanan = $request->input('id_pesanan');
        $pesanan = json_decode($request->get('pesanan'), true);

        // Meload data NotaPembeli berdasarkan no_nota
        $notaPembeli = NotaPembeli::where('no_nota', $no_nota)->with('PesananPembeli')->first();
        // DB::beginTransaction();
        // Memeriksa apakah NotaPembeli ditemukan
        if ($notaPembeli) {
            // Menghapus setiap PesananPembeli dan mengembalikan stok barang
            $pesananPembeli = PesananPembeli::where('id_nota', $notaPembeli->id_nota)->where('id_pesanan', $id_pesanan)->first();
            // Jika data PesananPembeli ditemukan
            if ($pesananPembeli) {
                $barangData = Barang::where('id_barang', $pesananPembeli->id_barang)->lockForUpdate()->first();
                if (empty($barangData)) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Gagal memasukkan barang, barang tidak ada');
                }
                $barangData->stok = $barangData->stok + $pesananPembeli->jumlah_pembelian - $pesanan['jumlah_pesanan'];
                $barangData->save();




                // Diskon Section
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

                // End Diskon

                // pesanan Pembeli
                $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                $pesananPembeli->id_diskon = $diskonId;
                $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                $pesananPembeli->harga = $hargaSetelahDiskon;
                $pesananPembeli->diskon = $hargaDiskon;
                $pesananPembeli->save();
                // End pesanan Pembeli




                // Mengembalikan respons sukses
                return response()->json(['message' => 'Pesanan berhasil dihapus']);
            } else {
                $barangData = Barang::where('hash_id_barang', $pesanan['id_barang'])->lockForUpdate()->first();
                if (empty($barangData)) {
                    // DB::rollBack();
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

                // Buat data baru untuk PesananPembeli yang terhubung dengan NotaPembeli dan Barang
                $pesananPembeli = new PesananPembeli;
                $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian
                $pesananPembeli->id_diskon = $diskonId;
                $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
                $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
                $pesananPembeli->harga = $hargaSetelahDiskon;
                $pesananPembeli->diskon = $hargaDiskon;
                $pesananPembeli->save();
            }




            // Update Nota Pembelian
            $this->notaPembelianUpdate($notaPembeli);


            return response()->json(['message' => 'Berhasil memperbarui nota pembelian', 'data' => [
                'id_pesanan' => $pesananPembeli->id_pesanan
            ]]);

            // DB::commit();
        } else {
            // NotaPembeli tidak ditemukan, kembalikan pesan error
            return response()->json(['message' => 'NotaPembeli tidak ditemukan'], 404);
        }
    }
}
