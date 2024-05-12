<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\BukubesarModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            AkunBayarSeeder::class,
            // BukubesarSeeder::class,
            UserSeeder::class,
            // TipeBarangSeeder::class,
            // PemasokSeeder::class,
            // BarangSeeder::class,
          
            DiskonSeeder::class,
            // PesananSeeder::class,
            // ZulfikarData::class,
            // MigrasiData::class
        ]);
    }
}
