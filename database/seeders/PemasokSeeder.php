<?php

namespace Database\Seeders;

use App\Models\PemasokBarang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PemasokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $PemasokData = [
            'nama_pemasok' => 'Nama Pemasok',
            'no_telp_pemasok' => '123456789',
            'alamat_pemasok' => 'Alamat Pemasok',
        ];

        PemasokBarang::create($PemasokData);
    }
}
