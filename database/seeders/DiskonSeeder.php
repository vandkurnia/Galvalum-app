<?php

namespace Database\Seeders;

use App\Models\DiskonModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiskonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data diskon dengan tipe persentase
        DiskonModel::create([
            'kode_diskon' => 'DISC10',
            'nama_diskon' => 'Diskon 10%',
            'type' => 'percentage',
            'besaran' => '10',
            'status' => 'AKTIF',
         
        ]);

        // Data diskon dengan tipe jumlah
        DiskonModel::create([
        
            'kode_diskon' => 'DISC50K',
            'nama_diskon' => 'Diskon Rp 50.000',
            'type' => 'amount',
            'besaran' => '50000',
            'status' => 'AKTIF',
    
        ]);
    }
}
