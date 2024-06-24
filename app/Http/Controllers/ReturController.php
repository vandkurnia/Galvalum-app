<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PesananPembeli;
use App\Models\Retur;
use App\Models\Retur\ReturPemasokModel;
use App\Models\Retur\ReturPembeliModel;
use Illuminate\Http\Request;

class ReturController extends Controller
{
    public function index()
    {
        $dataReturPemasok = ReturPemasokModel::with('pemasok')->where('hidden', 'no')->get();
        $dataReturPembeli = ReturPembeliModel::with('pembeli')->where('hidden', 'no')->get();
        return view('retur.retur', compact('dataReturPemasok', 'dataReturPembeli'));
    }
    
}
