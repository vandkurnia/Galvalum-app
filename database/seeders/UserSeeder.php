<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        // Ambil ID terakhir dari tabel users
        $lastId = User::max('id_admin');

        // Jika tidak ada data, atur ID ke 1
        $idAdmin = $lastId ? $lastId + 1 : 1;
        // Kombinasi id_admin + user + timestamp
        $combinedString =  $idAdmin . '|users|' . time();

        // Buat hash SHA-256
        $hash = hash('sha256', (string) $combinedString);
        User::create([
            'nama_admin' => 'Admin',
            'no_telp_admin' => '123456789',
            'email_admin' => 'admin@galvalum.com',
            'password' => Hash::make('password'),
            'hash_id_admin' => $hash,
        ]);
        // $admin = [
        //     [
        //         'nama_admin' => 'Super Admin',
        //         'no_telp_admin' => '08123xxxxxxx',
        //         'email' => 'admin@example.com',
        //         'password' => bcrypt('admin123'),
        //     ],
        // ];

        // foreach ($admin as $data) {
        //     User::create($data);
        // }
    }
}
