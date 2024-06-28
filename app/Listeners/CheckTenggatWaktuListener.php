<?php

namespace App\Listeners;

use App\Events\CheckTenggatWaktu;
use App\Models\Barang;
use App\Models\CustomNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckTenggatWaktuListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        // dd("taknak");
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CheckTenggatWaktu $event)
    {
        Log::info('Running CheckTenggatWaktuListener');

        $today = Carbon::today();

        $query = "
                SELECT *
                FROM nota_pembelis
                WHERE DATE(tenggat_bayar) = ?
                AND total > (nominal_terbayar + dp)
            ";

        $notas = DB::select($query, [$today]);

        foreach ($notas as $nota) {
            $exists = CustomNotification::where('type', 'piutang')
                ->where('id_data', $nota->id_nota)
                ->exists();

            if (!$exists) {
                CustomNotification::create([
                    'type' => 'piutang',
                    'id_data' => $nota->id_nota,
                    'icon' => 'fas fa-exclamation-triangle text-white',
                    'message' => "NotaPembeli No {$nota->no_nota} jatuh tempo pembayaran pada hari ini.",
                ]);
            }
        }

        $barangs = Barang::whereDate('tenggat_bayar', $today)->get();

        foreach ($barangs as $barang) {
            $exists = CustomNotification::where('type', 'hutang')
                ->where('id_data', $barang->id_barang)
                ->exists();

            if (!$exists) {
                CustomNotification::create([
                    'type' => 'hutang',
                    'id_data' => $barang->id_barang,
                    'icon' => 'fas fa-exclamation-triangle text-white',
                    'message' => "Barang {$barang->nama_barang} jatuh tempo pembayaran pada hari ini.",
                ]);
            }
        }

        Log::info('Completed CheckTenggatWaktuListener');
    }
}
