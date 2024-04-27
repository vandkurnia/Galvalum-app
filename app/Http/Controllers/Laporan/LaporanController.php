<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\NotaPembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function laporanOmzet()
    {

        $request = new Request;
        $tanggalDariInput = $request->get('tanggal') ?? date('Y-m-d');

        $tanggalSaatIni = date('Y-m-d', strtotime($tanggalDariInput));
        $dataNotaPembelian = DB::select(
            '
            SELECT 
                DATE(nota_pembelis.created_at) AS created_at,
                COUNT(pesanan_pembelis.id_pesanan) AS total_pesanan,
                SUM(nota_pembelis.total) AS omzet
            FROM 
                nota_pembelis
            JOIN 
                pesanan_pembelis ON pesanan_pembelis.id_nota = nota_pembelis.id_nota
            WHERE 
                DATE(nota_pembelis.created_at) = ?
            GROUP BY 
                created_at
            ',
            [$tanggalSaatIni]
        );

        $dataNotaPembelian = json_decode(json_encode($dataNotaPembelian), true);

        // Logika untuk mengambil data dan menampilkan laporan omzet
        return view('laporan.laporanomzet', compact('dataNotaPembelian'));
    }

    public function laporanHutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan hutang
        return view('laporan.laporanhutang');
    }

    public function laporanPiutang()
    {


        $request = new Request;
        $tanggalDariInput = $request->get('tanggal') ?? date('Y-m-d');

        $tanggalSaatIni = date('Y-m-d', strtotime($tanggalDariInput));
        $dataNotaPembelian = DB::select(
            '
            SELECT 
            nota_pembelis.id_nota,
            pembelis.nama_pembeli,
            pembelis.no_hp_pembeli,
            COUNT(pesanan_pembelis.id_pesanan) AS total_pembelian,
            DATE(nota_pembelis.created_at) AS tanggal_pembelian,
            nota_pembelis.total,
            nota_pembelis.nominal_terbayar as terbayar,
            nota_pembelis.tenggat_bayar as jatuh_tempo,
            CASE
                WHEN nota_pembelis.total > nota_pembelis.nominal_terbayar THEN "Belum Lunas"
                WHEN nota_pembelis.total < nota_pembelis.nominal_terbayar THEN "Kelebihan"
                ELSE "Lunas"
            END AS status_bayar
        FROM 
            nota_pembelis
        JOIN 
            pesanan_pembelis ON pesanan_pembelis.id_nota = nota_pembelis.id_nota
        JOIN 
            pembelis ON pembelis.id_pembeli = nota_pembelis.id_pembeli
        JOIN
            nota_bukubesar ON nota_bukubesar.id_nota = nota_pembelis.id_nota
        JOIN
            bukubesar ON bukubesar.id_bukubesar = nota_bukubesar.id_bukubesar
        WHERE 
            bukubesar.kategori = "transaksi" AND bukubesar.sub_kategori = "PIUTANG" 
        GROUP BY
            nota_pembelis.id_nota, pembelis.nama_pembeli, pembelis.no_hp_pembeli, nota_pembelis.total, nota_pembelis.tenggat_bayar, nota_pembelis.created_at, nota_pembelis.nominal_terbayar
        
  
            ',
            []
        );


        $dataNotaPembelian = json_decode(json_encode($dataNotaPembelian), true);
        
        // Logika untuk mengambil data dan menampilkan laporan piutang
        return view('laporan.laporanpiutang', compact('dataNotaPembelian'));
    }

    public function kasKeluar()
    {
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar');
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
