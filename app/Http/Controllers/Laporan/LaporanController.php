<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use App\Models\ModalTambahanModel;
use App\Models\BukubesarModel;
use App\Models\AkunBayarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;
use League\Csv\Writer;
use PDF;
use App\Models\NotaPembeli;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{

    public function generateCSV(Request $request)
    {
        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $modal_tambahan = BukubesarModel::where('kategori', $kategori)->get();
        foreach ($modal_tambahan as $kk) {
            $kk->tanggal = Carbon::parse($kk->tanggal);
        }

        $csv = view('csv.modal_tambahan', compact('modal_tambahan'))->render();

        // Set header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan Modal Tambahan.csv"',
        ];

        // Mengembalikan response CSV ke browser
        return Response::stream(function () use ($csv) {
            echo $csv;
        }, 200, $headers);
    }

    public function kaskeluarCSV(Request $request)
    {
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();
        $kas_keluar = BukubesarModel::where('kategori', $kategori)->get();

        foreach ($kas_keluar as $kk) {
            $kk->tanggal = Carbon::parse($kk->tanggal);
        }

        $csv = view('csv.kas_keluar', compact('kas_keluar'))->render();

        // Set header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan Kas Keluar.csv"',
        ];

        // Mengembalikan response CSV ke browser
        return Response::stream(function () use ($csv) {
            echo $csv;
        }, 200, $headers);
    }

    public function generatePDF(Request $request)
    {
        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_modal_tambahan = BukubesarModel::where('kategori', $kategori)
            ->get();

        $pdf = PDF::loadView('pdf.invoice', compact('laporan_modal_tambahan'));

        return $pdf->download('Laporan Modal Tambahan.pdf');
    }

    public function kaskeluarPDF(Request $request)
    {
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_kas_keluar = BukubesarModel::where('kategori', $kategori)
            ->get();
        // Ambil tanggal dari inputan form, jika tidak ada, gunakan null

        // Load view PDF dengan data yang sudah difilter
        $pdf = PDF::loadView('pdf.kas', compact('laporan_kas_keluar'));

        // Download PDF
        return $pdf->download('Laporan Kas Keluar.pdf');
    }

    public function laporanOmzet(Request $request)
    {
        $tanggalDariInput = $request->get('tanggal') ?? date('Y-m-d');

        $tanggalSaatIni = date('Y-m-d', strtotime($tanggalDariInput));
        // Query to get omzet grouped by item name
        $dataNotaPembelian = DB::select(
            '
        SELECT 
            barangs.nama_barang,
            SUM(pesanan_pembelis.jumlah_pembelian) AS jumlah_pembelian,
            pesanan_pembelis.jenis_pembelian,
            SUM(pesanan_pembelis.jumlah_pembelian * pesanan_pembelis.harga) AS omzet
        FROM barangs
        JOIN 
             pesanan_pembelis ON pesanan_pembelis.id_barang = barangs.id_barang AND pesanan_pembelis.deleted_at IS NULL
        JOIN nota_pembelis ON nota_pembelis.id_nota = pesanan_pembelis.id_nota AND nota_pembelis.deleted_at IS NULL
        WHERE 
            DATE(nota_pembelis.tanggal_penyelesaian) = ? AND nota_pembelis.total = nota_pembelis.nominal_terbayar
        GROUP BY 
            barangs.nama_barang, pesanan_pembelis.jenis_pembelian
        ',
            [$tanggalSaatIni]
        );

        $dataNotaPembelian = json_decode(json_encode($dataNotaPembelian), true);

        // Logika untuk mengambil data dan menampilkan laporan omzet
        return view('laporan.laporanomzet', compact('dataNotaPembelian', 'tanggalSaatIni'));
    }

    public function laporanHutang(Request $request)
    {

        // $dateToFinisihConstruction = date('Y-m-d H:i:s', strtotime('2024-05-22 23:59:59'));
        $tanggalDariInput = $request->get('tanggal') ?? date('Y-m-d');

        $tanggalSaatIni = date('Y-m-d', strtotime($tanggalDariInput));
        $dataLaporanHutang = DB::select(
            '
                SELECT
                    barangs.hash_id_barang as id_barang,
                    pemasok_barangs.nama_pemasok,
                    barangs.nama_barang,
                    barangs.stok as total_pesanan,
                    barangs.updated_at as tanggal_stok,
                    barangs.created_at as tanggal_stok_alt,
                    barangs.created_at as jatuh_tempo_alt,
                    barangs.total as harga_bayar,
                    barangs.nominal_terbayar as jumlah_terbayar,
                    barangs.tenggat_bayar as jatuh_tempo,
                    CASE
                    WHEN barangs.total > barangs.nominal_terbayar THEN "Belum Lunas"
                    WHEN barangs.total < barangs.nominal_terbayar THEN "Kelebihan"
                    ELSE "Lunas"
                    END AS status_pembayaran
                FROM
                    `barangs`
                LEFT JOIN
                    pemasok_barangs ON pemasok_barangs.id_pemasok = barangs.id_pemasok
              
               
                WHERE barangs.nominal_terbayar < barangs.total AND barangs.deleted_at IS NULL
                ',
            []
        );
        // $dataLaporanHutang = DB::select(
        //     '
        //         SELECT
        //             barangs.hash_id_barang as id_barang,
        //             pemasok_barangs.nama_pemasok,
        //             barangs.nama_barang,
        //             SUM(stok_barang.stok_masuk - stok_barang.stok_keluar) as total_pesanan,
        //             pemasok_barangs.created_at as tanggal_stok,
        //             barangs.total as harga_bayar,
        //             barangs.nominal_terbayar as jumlah_terbayar,
        //             barangs.tenggat_bayar as jatuh_tempo,
        //             CASE
        //             WHEN barangs.total > barangs.nominal_terbayar THEN "Belum Lunas"
        //             WHEN barangs.total < barangs.nominal_terbayar THEN "Kelebihan"
        //             ELSE "Lunas"
        //             END AS status_pembayaran
        //         FROM
        //             `barangs`
        //         LEFT JOIN
        //             pemasok_barangs ON pemasok_barangs.id_pemasok = barangs.id_pemasok
        //         JOIN
        //             stok_barang ON stok_barang.id_barang = barangs.id_barang
                
        //         LEFT JOIN
        //             bukubesar ON bukubesar.id_bukubesar = bukubesar_barang.id_bukubesar
        //         WHERE barangs.nominal_terbayar < barangs.total
        //         GROUP BY
        //             barangs.hash_id_barang, pemasok_barangs.nama_pemasok, barangs.nama_barang, pemasok_barangs.created_at, barangs.total, barangs.nominal_terbayar, barangs.tenggat_bayar; 
        //         ',
        //     []
        // );

        // Mengubah hasil query menjadi array
        $dataLaporanHutangArray = collect($dataLaporanHutang)->map(function ($item) {
            return (array) $item;
        })->toArray();

        return view('laporan.laporanhutang', ['dataLaporanHutang' => $dataLaporanHutangArray    ]);
    }

    public function laporanPiutang(Request $request)
    {

        if ($request->has('api')) {
            return response()->json([
                'code' => 200,
                'message' => 'OK'
            ]);
        }
        $tanggal = $request->get('tanggal');

        // Dumping the GET request parameters (For debugging purposes)

        // Default to today's date if no date is provided
        $tanggalDariInput = $tanggal ?? null;

        $query = '
            SELECT 
                nota_pembelis.id_nota,
                pembelis.nama_pembeli,
                pembelis.no_hp_pembeli,
                SUM(pesanan_pembelis.jumlah_pembelian) AS total_pembelian,
                DATE(nota_pembelis.created_at) AS tanggal_pembelian,
                nota_pembelis.total,
                (nota_pembelis.nominal_terbayar + nota_pembelis.dp) as terbayar,
                nota_pembelis.tenggat_bayar as jatuh_tempo,
                CASE
                    WHEN nota_pembelis.total > (nota_pembelis.nominal_terbayar + nota_pembelis.dp) THEN "Belum Lunas"
                    WHEN nota_pembelis.total < (nota_pembelis.nominal_terbayar + nota_pembelis.dp) THEN "Kelebihan"
                    ELSE "Lunas"
                END AS status_bayar
            FROM 
                nota_pembelis
            JOIN 
                pesanan_pembelis ON pesanan_pembelis.id_nota = nota_pembelis.id_nota
            JOIN 
                pembelis ON pembelis.id_pembeli = nota_pembelis.id_pembeli
         
            WHERE 
                 (nota_pembelis.nominal_terbayar + nota_pembelis.dp) < nota_pembelis.total AND nota_pembelis.deleted_at IS NULL
            
        ';

        if ($tanggal) {
            $query .= ' AND DATE(nota_pembelis.created_at) = ? ';
            $query .= 'GROUP BY nota_pembelis.id_nota, pembelis.nama_pembeli, pembelis.no_hp_pembeli, nota_pembelis.total, nota_pembelis.tenggat_bayar, nota_pembelis.created_at, nota_pembelis.nominal_terbayar, nota_pembelis.dp';
            $query .= " ORDER BY nota_pembelis.created_at DESC";
            
            $dataNotaPembelian = DB::select($query, [$tanggal]);
        } else {
            $query .= 'GROUP BY nota_pembelis.id_nota, pembelis.nama_pembeli, pembelis.no_hp_pembeli, nota_pembelis.total, nota_pembelis.tenggat_bayar, nota_pembelis.created_at, nota_pembelis.nominal_terbayar, nota_pembelis.dp';
            $query .= " ORDER BY nota_pembelis.created_at DESC";
            $dataNotaPembelian = DB::select($query, []);
        }





        // Lunas
        // Query for Lunas
        $queryLunasdanKelebihan = '
          SELECT 
              nota_pembelis.id_nota,
              pembelis.nama_pembeli,
              pembelis.no_hp_pembeli,
              SUM(pesanan_pembelis.jumlah_pembelian) AS total_pembelian,
              DATE(nota_pembelis.created_at) AS tanggal_pembelian,
              nota_pembelis.total,
              nota_pembelis.nominal_terbayar as terbayar,
              nota_pembelis.tenggat_bayar as jatuh_tempo,
              "Lunas" AS status_bayar
          FROM 
              nota_pembelis
          JOIN 
              pesanan_pembelis ON pesanan_pembelis.id_nota = nota_pembelis.id_nota
          JOIN 
              pembelis ON pembelis.id_pembeli = nota_pembelis.id_pembeli
          WHERE 
            (nota_pembelis.nominal_terbayar + nota_pembelis.dp) = nota_pembelis.total OR (nota_pembelis.nominal_terbayar + nota_pembelis.dp) > nota_pembelis.total AND nota_pembelis.piutang_is_visible = "yes"  AND nota_pembelis.deleted_at IS NULL
              
      ';

        if ($tanggal) {
            $queryLunasdanKelebihan .= ' AND DATE(nota_pembelis.created_at) = ? ';
            $queryLunasdanKelebihan .= 'GROUP BY nota_pembelis.id_nota, pembelis.nama_pembeli, pembelis.no_hp_pembeli, nota_pembelis.total, nota_pembelis.tenggat_bayar, nota_pembelis.created_at, nota_pembelis.nominal_terbayar';
            $queryLunasdanKelebihan .= " ORDER BY nota_pembelis.created_at DESC";
            $dataNotaPembelianLunasdanKelebihan = DB::select($queryLunasdanKelebihan, [$tanggal]);
        } else {
            $queryLunasdanKelebihan .= 'GROUP BY nota_pembelis.id_nota, pembelis.nama_pembeli, pembelis.no_hp_pembeli, nota_pembelis.total, nota_pembelis.tenggat_bayar, nota_pembelis.created_at, nota_pembelis.nominal_terbayar';
            $queryLunasdanKelebihan .= " ORDER BY nota_pembelis.created_at DESC";
            $dataNotaPembelianLunasdanKelebihan = DB::select($queryLunasdanKelebihan, []);
        }

        // Piutang
        $dataNotaPembelian = json_decode(json_encode($dataNotaPembelian), true);

        // Lunas dan Kelebihan
        $NotaPembelianLunasdanKelebihan =  json_decode(json_encode($dataNotaPembelianLunasdanKelebihan), true);
        // Logika untuk mengambil data dan menampilkan laporan piutang
        return view('laporan.laporanpiutang', compact('dataNotaPembelian', 'NotaPembelianLunasdanKelebihan'));
    }

    public function kasKeluar(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_kas_keluar = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori)
            ->get();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar', compact('laporan_kas_keluar', 'dataAkunBayar'));
    }

    public function simpanKas(Request $request)
    {
        $request->validate([
            'id_akunbayar' => 'nullable|exists:akun_bayar,hash_id_akunbayar',
            'keterangan' => 'required',
            'kredit' => 'required',
            'tanggal' => 'required',

        ]);

        $akunBayar = AkunBayarModel::where('hash_id_akunbayar', $request['id_akunbayar'])->first();

        // Array data user dari request
        $Keluar = [
            'id_akunbayar' => $akunBayar->id_akunbayar,
            'kategori' => $request->input('kategori', 'pengeluaran'),
            'keterangan' => $request->keterangan,
            'kredit' => $request->kredit,
            'debit' => $request->input('debit', '0'),
            'tanggal' => $request->tanggal,

        ];
        BukubesarModel::create($Keluar);

        return redirect()->route('laporan.kaskeluar')->with('success', 'Kas Keluar herhasil ditambahkan');
    }

    public function editKas(Request $request, $id)
    {
        $laporan_kas_keluar = BukubesarModel::where('id_bukubesar', $id)->first();
        if (!$laporan_kas_keluar) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('laporan.kaskeluar_edit', compact('laporan_kas_keluar'))->render()
        ], 200);
    }

    public function updateKas(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required',
            'kredit' => 'required',
            'tanggal' => 'required',

        ]);
        // dd("heheha");

        $laporan_kas_keluar = BukubesarModel::where('id_bukubesar', $id)->first();

        $laporan_kas_keluar->update($request->all());

        return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataTipeBarang = BukubesarModel::where('id_bukubesar', $id)->first();
        if ($dataTipeBarang) {
            $dataTipeBarang->delete();

            return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang dihapus');
        } else {
            return redirect()->route('laporan.kaskeluar')->with('error', 'Tipe barang gagal dihapus');
        }
    }

    public function filterKas(Request $request)
    {
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();

        $tanggal = $request->input('tanggal');


        $laporan_kas_keluar = KasKeluar::where('kategori', $kategori)->whereDate('tanggal', '=', $tanggal)->get();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar', compact('laporan_kas_keluar', 'dataAkunBayar'));
    }

    public function modalTambahan(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_modal_tambahan = BukubesarModel::where('tanggal', $tanggal)->where('kategori', $kategori)->get();
        // Logika untuk mengambil data dan menampilkan laporan modal tambahan
        return view('laporan.modal_tambahan', compact('laporan_modal_tambahan', 'dataAkunBayar'));
    }

    public function editModal(Request $request, $id)
    {
        $laporan_modal_tambahan = BukubesarModel::where('id_bukubesar', $id)->first();
        if (!$laporan_modal_tambahan) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('laporan.modaledit', compact('laporan_modal_tambahan'))->render()
        ], 200);
    }

    public function updateModal(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required',
            'debit' => 'required',
            'tanggal' => 'required',

        ]);
        // dd("heheha");

        $laporan_modal_tambahan = BukubesarModel::where('id_bukubesar', $id)->first();

        $laporan_modal_tambahan->update($request->all());

        return redirect()->route('laporan.modaltambahan')->with('success', 'Modal Tambahan berhasil diupdate');
    }

    public function modaldestroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $laporan_modal_tambahan = BukubesarModel::where('id_bukubesar', $id)->first();
        if ($laporan_modal_tambahan) {
            $laporan_modal_tambahan->delete();

            return redirect()->route('laporan.modaltambahan')->with('success', 'Modal tambahan dihapus');
        } else {
            return redirect()->route('laporan.modaltambahan')->with('error', 'Modal tambahan gagal dihapus');
        }
    }

    public function filterModal(Request $request)
    {
        $tanggal = $request->tanggal;

        $laporan_modal_tambahan = BukubesarModel::whereDate('tanggal', '=', $tanggal)->get();

        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_modal_tambahan = BukubesarModel::where('kategori', $kategori)->get();

        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.modal_tambahan', compact('laporan_modal_tambahan', 'kategori', 'dataAkunBayar'));
    }

    public function simpanModal(Request $request)
    {
        $request->validate([
            'id_akunbayar' => 'nullable|exists:akun_bayar,hash_id_akunbayar',
            'keterangan' => 'required',
            'debit' => 'required',
            'tanggal' => 'required',

        ]);

        $akunBayar = AkunBayarModel::where('hash_id_akunbayar', $request['id_akunbayar'])->first();

        // Array data user dari request
        $Keluar = [
            'id_akunbayar' => $akunBayar->id_akunbayar,
            'kategori' => $request->input('kategori', 'modal tambahan'),
            'keterangan' => $request->keterangan,
            'debit' => $request->debit,
            'kredit' => $request->input('kredit', '0'),
            'tanggal' => $request->tanggal,

        ];
        BukubesarModel::create($Keluar);

        return redirect()->route('laporan.modaltambahan')->with('success', 'Modal Tambahan herhasil ditambahkan');
    }

    public function labaRugi(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        /*  $tanggal = $request->input('tanggal', '2024-04-25'); */
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal_tambahan = $request->input('kategori', 'modal tambahan'); // Kategori default adalah 'transaksi'
        $kategori_pengeluaran = $request->input('kategori', 'pengeluaran'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        // $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
        //     ->where('kategori', $kategori)
        //     ->get();

        // $penjualan_kotor = BukubesarModel::join('nota_bukubesar', 'bukubesar.id_bukubesar', '=', 'nota_bukubesar.id_bukubesar')
        //     ->join('nota_pembelis', function ($join) {
        //         $join->on('nota_bukubesar.id_nota', '=', 'nota_pembelis.id_nota')
        //             ->where('nota_pembelis.total', '=', DB::raw('nota_pembelis.nominal_terbayar'))
        //             ->whereNull('nota_pembelis.deleted_at');
        //     })
        //     ->where('bukubesar.tanggal', $tanggal)
        //     ->where('bukubesar.kategori', $kategori)
        //     ->whereNull('bukubesar.deleted_at')
        //     ->select('bukubesar.*')
        //     ->get();
        $penjualan_kotor = BukubesarModel::whereDate('bukubesar.created_at', $tanggal)
            ->where('bukubesar.kategori', $kategori)
            ->whereNull('bukubesar.deleted_at')
            ->select('bukubesar.*')
            ->get();

        $modal_tambahan = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal_tambahan)
            ->get();

        $keluar = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_pengeluaran)
            ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal)->where('debit', '>', 0)
            ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
            ->get();

        $total_modal_darurat = $modal_darurat->sum('kredit');
        $total_modal_tambahan = $modal_tambahan->sum('debit');
        $total_keluar = $keluar->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $total_modal_tambahan;
        $laba_bersih = $laba_kotor - $total_keluar;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        return view('laporan.laba_rugi', compact('modal_tambahan', 'keluar', 'total_modal_tambahan', 'total_keluar', 'total_penjualan_kotor', 'penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer'));
    }

    public function labaRugiPDF(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        // $tanggal = $request->input('tanggal', '2024-04-25');
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal_tambahan = $request->input('kategori', 'modal tambahan'); // Kategori default adalah 'transaksi'
        $kategori_pengeluaran = $request->input('kategori', 'pengeluaran'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori)
            ->get();

        $modal_tambahan = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal_tambahan)
            ->get();

        $keluar = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_pengeluaran)
            ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal)->where('debit', '>', 0)
            ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
            ->get();

        $total_modal_darurat = $modal_darurat->sum('kredit');
        $total_modal_tambahan = $modal_tambahan->sum('debit');
        $total_keluar = $keluar->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $total_modal_tambahan;
        $laba_bersih = $laba_kotor - $total_keluar;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        $namaHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan indeks hari dari tanggal yang diberikan
        $indeksHari = date('w', strtotime($tanggal));

        // Mendapatkan nama hari dalam bahasa Indonesia
        $hari = $namaHari[$indeksHari];
        $tanggal = strftime("%d %B %Y", strtotime($tanggal));

        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        $pdf = PDF::loadView('pdf.laba_rugi', compact('modal_tambahan', 'keluar', 'total_modal_tambahan', 'total_keluar', 'total_penjualan_kotor', 'penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer', 'hari', 'tanggal'));

        return $pdf->download('Laporan Laba Rugi.pdf');
    }

    public function LabaRugiCSV(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        // $tanggal = $request->input('tanggal', '2024-04-25');
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal_tambahan = $request->input('kategori', 'modal tambahan'); // Kategori default adalah 'transaksi'
        $kategori_pengeluaran = $request->input('kategori', 'pengeluaran'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori)
            ->get();

        $modal_tambahan = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal_tambahan)
            ->get();

        $keluar = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_pengeluaran)
            ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal)->where('debit', '>', 0)
            ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
            ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
            ->get();

        $total_modal_darurat = $modal_darurat->sum('kredit');
        $total_modal_tambahan = $modal_tambahan->sum('debit');
        $total_keluar = $keluar->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $total_modal_tambahan;
        $laba_bersih = $laba_kotor - $total_keluar;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        $namaHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan indeks hari dari tanggal yang diberikan
        $indeksHari = date('w', strtotime($tanggal));

        // Mendapatkan nama hari dalam bahasa Indonesia
        $hari = $namaHari[$indeksHari];
        $tanggal = strftime("%d %B %Y", strtotime($tanggal));

        // Buat string CSV dari data
        $csv = view('csv.laba_rugi', compact('modal_tambahan', 'keluar', 'total_modal_tambahan', 'total_keluar', 'total_penjualan_kotor', 'penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer', 'hari', 'tanggal'))->render();

        // Set header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan Laba Rugi.csv"',
        ];

        // Mengembalikan response CSV ke browser
        return Response::stream(function () use ($csv) {
            echo $csv;
        }, 200, $headers);
    }
}
