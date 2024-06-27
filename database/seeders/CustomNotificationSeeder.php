<?php

namespace Database\Seeders;

use App\Models\CustomNotification;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomNotification::create([
            'type' => 'piutang',
            'id_data' => 1,
            'icon' => 'fas fa-exclamation-triangle text-white',
            'message' => 'There is a due payment for a purchase note.',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        CustomNotification::create([
            'type' => 'hutang',
            'id_data' => 2,
            'icon' => 'fas fa-exclamation-triangle text-white',
            'message' => 'There is a due payment for an item.',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
