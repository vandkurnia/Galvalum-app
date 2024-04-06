<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PemasokBarangController extends Controller
{
    public function index()
    {
        return view('master.pemasokbarang.index');
    }
}
