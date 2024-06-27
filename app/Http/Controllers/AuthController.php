<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\CustomNotification;
use App\Models\NotaPembeli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        $data['title'] = 'Masuk';
        return view('guest.login', $data);
    }
    public function checkTenggatWaktu()
    {
        $today = Carbon::today();

        // Check NotaPembeli with tenggat_waktu today
        $notas = NotaPembeli::whereDate('tenggat_bayar', $today)->whereRaw('total =! (nominal_terbayar + dp)')->get();
        // dd($notas);
        foreach ($notas as $nota) {
            // Check if notification already exists
            $exists = CustomNotification::where('type', 'piutang')
                ->where('id_data', $nota->id_nota)
                ->exists();
               

            if (!$exists) {
                // Create notification
                CustomNotification::create([
                    'type' => 'piutang',
                    'id_data' => $nota->id,
                    'icon' => 'fas fa-exclamation-triangle text-white',
                    'message' => "NotaPembeli No {$nota->no_nota} diperlukan pembayaran.",
                ]);
            }
        }

        // Check Barang with tenggat_waktu today
        $barangs = Barang::whereDate('tenggat_bayar', $today)->get();

        foreach ($barangs as $barang) {
            // Check if notification already exists
            $exists = CustomNotification::where('type', 'hutang')
                ->where('id_data', $barang->id)
                ->exists();

            if (!$exists) {
                // Create notification
                CustomNotification::create([
                    'type' => 'hutang',
                    'id_data' => $barang->id,
                    'icon' => 'fas fa-exclamation-triangle text-white',
                    'message' => "Barang  {$barang->nama_barang} diperlukan pembayaran.",
                ]);
            }
        }
    }


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
            $this->checkTenggatWaktu();
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
