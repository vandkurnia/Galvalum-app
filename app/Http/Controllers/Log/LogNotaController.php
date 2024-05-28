<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Models\Log\LogNotaModel;
use Illuminate\Http\Request;

class LogNotaController extends Controller
{
    public function index($id_nota)
    {
        
        $logNotaData = LogNotaModel::with('admin', 'nota')->where('id_nota', $id_nota)->get();
        
        return view('log.lognota', compact('logNotaData'));
    }
}
