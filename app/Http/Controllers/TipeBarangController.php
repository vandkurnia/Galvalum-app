<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TipeBarangController extends Controller
{
    public function index()
    {
        return view('master.tipebarang.index');
    }
}
