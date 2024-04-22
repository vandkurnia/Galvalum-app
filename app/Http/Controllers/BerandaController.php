<?php

namespace App\Http\Controllers;

use App\Models\DiskonModel;
use App\Models\NotaPembeli;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        $lastId = NotaPembeli::max('id_nota');
        $lastId = $lastId ? $lastId : 0; // handle jika tabel kosong
        $lastId++;
        $dataDiskon = DiskonModel::all();


        $no_nota = 'NT' . date('Y') . date('mdHis') . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        return view('beranda', compact('no_nota', 'dataDiskon'));
    }
}
