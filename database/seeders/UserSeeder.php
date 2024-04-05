<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
            [
                'nama_admin' => 'Super Admin',
                'no_telp_admin' => '08123xxxxxxx',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin123'),
            ],
            ];
    
            foreach ($admin as $data) {
                User::create($data);
            }  
    }
}
