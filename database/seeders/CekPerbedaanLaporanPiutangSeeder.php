<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CekPerbedaanLaporanPiutangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hitung baris pada koneksi default yang memenuhi kondisi
        $defaultIds = DB::table('nota_pembelis')
            ->whereRaw('nominal_terbayar + dp < total')
            ->whereNull('deleted_at')
            ->pluck('id_nota');

        $defaultCount = $defaultIds->count();

        // Hitung baris pada koneksi 'sql_galvalum_asli' yang memenuhi kondisi
        $otherIds = DB::connection('sql_galvalum_asli')
            ->table('nota_pembelis')
            ->whereRaw('nominal_terbayar < total')
            ->whereNull('deleted_at')
            ->pluck('id_nota');

        $otherCount = $otherIds->count();

        // Bandingkan dan tampilkan hasil di console
        if ($defaultCount == $otherCount) {
            echo "Total data keduanya sama: $defaultCount\n";
        } else {
            echo "Total data tidak sama: \n";
            echo "Default DB Count: $defaultCount\n";
            echo "sql_galvalum_asli DB Count: $otherCount\n";
        }

        // Persiapkan array untuk menyimpan hasil
        $result = [];

        // Tambahkan data dari default database
        foreach ($defaultIds as $id) {
            $result[$id] = [
                'db_saat_ini' => 'yes',
                'db_old' => 'no'
            ];
        }

        // Update data dari old database
        foreach ($otherIds as $id) {
            if (isset($result[$id])) {
                $result[$id]['db_old'] = 'yes';
            } else {
                $result[$id] = [
                    'db_saat_ini' => 'no',
                    'db_old' => 'yes'
                ];
            }
        }

        // Konversi array ke format JSON
        $jsonResult = json_encode($result, JSON_PRETTY_PRINT);

        // Simpan hasil ke dalam file JSON di folder public
        File::put(public_path('nota_checkingisgone.json'), $jsonResult);

        echo "File nota_checkingisgone.json berhasil disimpan.\n";
    }
}
