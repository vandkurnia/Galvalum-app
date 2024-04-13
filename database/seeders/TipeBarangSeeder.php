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
            ['nama_tipe' => 'Unit'],
            ['nama_tipe' => 'Boks'],
            ['nama_tipe' => 'Paket'],
            ['nama_tipe' => 'Palet'],
            ['nama_tipe' => 'Meter Kubik'],
            ['nama_tipe' => 'Palet'],
            ['nama_tipe' => 'Kilogran'],
            ['nama_tipe' => 'Coil'],
            ['nama_tipe' => 'Pipa'],
            ['nama_tipe' => 'Wire'],
            ['nama_tipe' => 'Angle'],
            ['nama_tipe' => 'Channel'],
            ['nama_tipe' => 'Rod'],
            ['nama_tipe' => 'Beam'],
            ['nama_tipe' => 'Plate'],
            ['nama_tipe' => 'Mesh']

        ];
        foreach ($tipeBarangSeeder as $dataTipe) {
            TipeBarang::create($dataTipe);
        }
    }
}
