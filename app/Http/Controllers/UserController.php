<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {

        $users = User::all();
        return view('master.user.index', compact('users'));
    }
    private function generateHashForUser(): string
    {
        // Ambil ID terakhir dari tabel users
        $lastId = User::max('id_admin');

        // Jika tidak ada data, atur ID ke 1
        $idAdmin = $lastId ? $lastId + 1 : 1;
        // Kombinasi id_admin + user + timestamp
        $combinedString =  $idAdmin . '|users|' . time();

        // Buat hash SHA-256
        $hash = hash('sha256', (string) $combinedString);
        return $hash;
    }
    public function edit(Request $request, $id)
    {
        $datauser = User::where('hash_id_admin', $id)->first();
        if (!$datauser) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('master.user.edit', compact('datauser'))->render()
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_admin' => 'required',
            'no_telp_admin' => 'required',
            'email_admin' => 'required|email|unique:users,email_admin',
            'password' => 'required',
        ]);



        // Array data user dari request
        $user = [
            'hash_id_admin' => $this->generateHashForUser(),
            'nama_admin' => $request->nama_admin,
            'no_telp_admin' => $request->no_telp_admin,
            'email_admin' => $request->email_admin,
            'password' => Hash::make($request->password),
        ];
        User::create($user);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_admin' => 'required',
            'no_telp_admin' => 'required',
            // 'email_admin' => 'required|email|unique:users,email_admin',
        ]);
        // dd("heheha");

        $user = User::where('hash_id_admin', $id)->first();
        // $user->nama_admin =$request->name;
        // $user->no_telp_admin = $request->no_telp_admin;
        // $user->email_admin = $request->email_admin;
        // // Setel field lainnya sesuai kebutuhan
        // $user->save();
        $user->update($request->all());

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $user = User::where('hash_id_admin', $id)->first();
        if ($user) {
            $user->delete();

            return redirect()->route('user.index')->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->route('user.index')->with('error', 'User gagal dihapus');
        }
    }
}
