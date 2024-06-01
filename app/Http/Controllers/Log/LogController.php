<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Log\LogNotaModel;
use App\Models\Log\LogStokBarangModel;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index($id_nota)
    {

        $logNotaData = LogNotaModel::with('admin', 'nota')->where('id_nota', $id_nota)->get();

        return view('log.lognota', compact('logNotaData'));
    }
    public function LogStokBarang($id_barang)
    {
        $barang = Barang::where('hash_id_barang', $id_barang)->first();
        $logStokBarang = LogStokBarangModel::with('admin', 'barang')->where('id_barang', $barang->id_barang)->get();


  
        return view('log.logstokbarang', compact('logStokBarang'));
    }
}
