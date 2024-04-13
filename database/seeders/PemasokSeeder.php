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

        for ($i = 0; $i < 10; $i++) {
            $PemasokData = [
                'nama_pemasok' => fake()->company(),
                'no_telp_pemasok' => fake()->e164PhoneNumber(),
                'alamat_pemasok' => fake()->address(),
            ];

            PemasokBarang::create($PemasokData);
        }
    }
}
