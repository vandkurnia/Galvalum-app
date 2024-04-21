<?php

namespace Database\Seeders;

use App\Models\AkunBayarModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AkunBayarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AkunBayarModel::create([
            'no_akun' => 'REKENINGUTAMA',
            'nama_akun' => 'Rekening Utama',
            'tipe_akun' => 'Utama',
            'saldo' => 0,
            'saldo_akhir' => 0,
            'saldo_anak' => 0,
        ]);
    }
}
