<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        $data['title'] = 'Masuk';
        return view('guest.login', $data);
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
