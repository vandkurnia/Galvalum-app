<?php

namespace Database\Seeders;

use App\Models\TipeBarang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipeBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $tipeBarangSeeder = [
            'nama_tipe' => 'Nama Tipe Barang',
        ];

        TipeBarang::create($tipeBarangSeeder);
    }
}
