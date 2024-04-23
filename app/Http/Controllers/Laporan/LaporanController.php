<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporanOmzet()
    {
        // Logika untuk mengambil data dan menampilkan laporan omzet
        return view('laporan.laporanomzet');
    }

    public function laporanHutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan hutang
        return view('laporan.laporanhutang');
    }

    public function laporanPiutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan piutang
        return view('laporan.laporanpiutang');
    }

    public function kasKeluar()
    {
        $laporan_kas_keluar = KasKeluar::all();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar', compact('laporan_kas_keluar'));
    }

    public function simpanKas(Request $request)
    {
        $request->validate([
            'nama_pengeluaran' => 'required',
            'deskripsi' => 'required',
            'jumlah_pengeluaran' => 'required',
            'tanggal' => 'required',

        ]);

        // Array data user dari request
        $Keluar = [
            'nama_pengeluaran' => $request->nama_pengeluaran,
            'deskripsi' => $request->deskripsi,
            'jumlah_pengeluaran' => $request->jumlah_pengeluaran,
            'tanggal' => $request->tanggal,

        ];
        KasKeluar::create($Keluar);

        return redirect()->route('laporan.kaskeluar')->with('success', 'Kas Keluar herhasil ditambahkan');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataTipeBarang = KasKeluar::where('id_kas_keluar', $id)->first();
        if ($dataTipeBarang) {
            $dataTipeBarang->delete();

            return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang dihapus');
        } else {
            return redirect()->route('laporan.kaskeluar')->with('error', 'Tipe barang gagal dihapus');
        }
    }

    public function filter(Request $request)
    {
        $tanggal = $request->tanggal;
        
        $users = User::whereDate('created_at','=',$tanggal)->get();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar',compact('users'));
    }

    public function modalTambahan()
    {
        // Logika untuk mengambil data dan menampilkan laporan modal tambahan
        return view('laporan.modal_tambahan');
    }

    public function labaRugi()
    {
        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        return view('laporan.laba_rugi');
    }
}
