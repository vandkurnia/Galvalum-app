<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DataHilangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the date range
        $startDate = '2024-06-02 00:00:00';
        $endDate = '2024-06-03 23:59:59';

        // Define the directory path
        $directory = public_path('datahilang');

        // Create the directory if it doesn't exist
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Fetch records from the nota_pembelis table using the 'sql_coba' connection
        $notaPembelis = DB::connection('sql_coba')->table('nota_pembelis')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Convert the result to JSON and save the data to nota_pembeli_hilang.json
        $notaJsonResult = $notaPembelis->toJson(JSON_PRETTY_PRINT);
        $notaFilePath = $directory . '/nota_pembeli_hilang.json';
        File::put($notaFilePath, $notaJsonResult);

        $this->command->info('Data has been exported to public/datahilang/nota_pembeli_hilang.json');

        // Fetch records from the pesanan_pembelis table using the 'sql_coba' connection
        $pesananPembelis = DB::connection('sql_coba')->table('pesanan_pembelis')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Convert the result to JSON and save the data to pesanan_pembeli_hilang.json
        $pesananJsonResult = $pesananPembelis->toJson(JSON_PRETTY_PRINT);
        $pesananFilePath = $directory . '/pesanan_pembeli_hilang.json';
        File::put($pesananFilePath, $pesananJsonResult);

        $this->command->info('Data has been exported to public/datahilang/pesanan_pembeli_hilang.json');

        // Fetch records from the pembelis table using the 'sql_coba' connection
        $pembelis = DB::connection('sql_coba')->table('pembelis')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Convert the result to JSON and save the data to pembeli_hilang.json
        $pembeliJsonResult = $pembelis->toJson(JSON_PRETTY_PRINT);
        $pembeliFilePath = $directory . '/pembeli_hilang.json';
        File::put($pembeliFilePath, $pembeliJsonResult);

        $this->command->info('Data has been exported to public/datahilang/pembeli_hilang.json');
    }
}
