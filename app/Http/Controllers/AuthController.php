<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\CustomNotification;
use App\Models\NotaPembeli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function index()
    {
        // $this->checkTenggatWaktu();
        $data['title'] = 'Masuk';
        return view('guest.login', $data);
    }
    // public function checkTenggatWaktu()
    // {
    //     $today = Carbon::today();

    //     // Construct the raw SQL query
    //     $query = "
    //             SELECT *
    //             FROM nota_pembelis
    //             WHERE DATE(tenggat_bayar) = ?
    //             AND total != (nominal_terbayar + dp)
    //         ";

    //     // Execute the query using DB::select
    //     $notas = DB::select($query, [$today]);

    //     foreach ($notas as $nota) {
    //         // Check if notification already exists
    //         $exists = CustomNotification::where('type', 'piutang')
    //             ->where('id_data', $nota->id_nota)
    //             ->exists();


    //         if (!$exists) {
     
    //             // Create notification
    //             CustomNotification::create([
    //                 'type' => 'piutang',
    //                 'id_data' => $nota->id_nota,
    //                 'icon' => 'fas fa-exclamation-triangle text-white',
    //                 'message' => "NotaPembeli No {$nota->no_nota} jatuh tempo pembayaran  pada hari ini.",
    //             ]);
    //         }
    //     }

    //     // Check Barang with tenggat_waktu today
    //     $barangs = Barang::whereDate('tenggat_bayar', $today)->get();
    
    //     foreach ($barangs as $barang) {
    //         // Check if notification already exists
    //         $exists = CustomNotification::where('type', 'hutang')
    //             ->where('id_data', $barang->id_barang)
    //             ->exists();

    //         if (!$exists) {
    //             // Create notification
    //             CustomNotification::create([
    //                 'type' => 'hutang',
    //                 'id_data' => $barang->id_barang,
    //                 'icon' => 'fas fa-exclamation-triangle text-white',
    //                 'message' => "Barang  {$barang->nama_barang} jatuh tempo pembayaran  pada hari ini.",
    //             ]);
    //         }
    //     }
    // }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $infologin = [
            'email_admin' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->has('remember') ? true : false;

        if (Auth::attempt($infologin, $remember)) {
            $request->session()->regenerate();
        
            return redirect('beranda');
        } else {
            return back()->withErrors('Email dan Password anda salah!')->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
