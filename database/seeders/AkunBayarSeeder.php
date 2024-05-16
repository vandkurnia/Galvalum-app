<?php

namespace Database\Seeders;

use App\Models\AkunBayarModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Uuid as UuidUuid;

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
            'no_akun' => '1000',
            'nama_akun' => 'Transaksi',
            'tipe_akun' => 'Utama',
            'saldo' => 0,
            'saldo_akhir' => 0,
            'saldo_anak' => 0,
        ]);


        DB::table('akun_bayar')->insert([
            [
                'hash_id_akunbayar' => (string) Uuid::uuid4()->toString(),
                'no_akun' => '1001',
                'nama_akun' => 'Bank',
                'tipe_akun' => 'Bank Transfer',
                'saldo' => 20000,
                'saldo_akhir' => 20000,
                'saldo_anak' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hash_id_akunbayar' => (string) Uuid::uuid4()->toString(),
                'no_akun' => '1001.01',
                'nama_akun' => 'Kas Kecil',
                'tipe_akun' => 'Bank Transfer',
                'saldo' => 40000,
                'saldo_akhir' => 40000,
                'saldo_anak' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hash_id_akunbayar' => (string) Uuid::uuid4()->toString(),
                'no_akun' => '1001.01.01',
                'nama_akun' => 'Kas Kuaci',
                'tipe_akun' => 'Bank Transfer',
                'saldo' => 60000,
                'saldo_akhir' => 60000,
                'saldo_anak' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hash_id_akunbayar' => (string) Uuid::uuid4()->toString(),
                'no_akun' => '1001.02',
                'nama_akun' => 'Mantap',
                'tipe_akun' => 'Bank Transfer',
                'saldo' => 90000,
                'saldo_akhir' => 90000,
                'saldo_anak' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'hash_id_akunbayar' => (string) Uuid::uuid4()->toString(),
                'no_akun' => '1001.03',
                'nama_akun' => 'Hehe',
                'tipe_akun' => 'Bank Transfer',
                'saldo' => 80000,
                'saldo_akhir' => 80000,
                'saldo_anak' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
