<?php

namespace App\Jobs;

use App\Models\Barang;
use App\Models\CustomNotification;
use App\Models\NotaPembeli;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckTenggatWaktuJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Job CheckTenggatWaktuJob started at: ' . now());
        $today = Carbon::today();

        // Check NotaPembeli with tenggat_bayar today
        $notas = NotaPembeli::whereDate('tenggat_bayar', $today)
            ->select('id_nota', 'no_nota')
            ->whereRaw('total != (nominal_terbayar + dp)')
            ->get();

        foreach ($notas as $nota) {
            // Check if notification already exists
            $exists = CustomNotification::where('type', 'piutang')
                ->where('id_data', $nota->id_nota)
                ->exists();

            if (!$exists) {
                // Create notification
                CustomNotification::create([
                    'type' => 'piutang',
                    'id_data' => $nota->id_nota,
                    'icon' => 'fas fa-exclamation-triangle text-white',
                    'message' => "NotaPembeli No {$nota->no_nota} jatuh tempo pembayaran pada hari ini.",
                ]);
            }
        }

        // Check Barang with tenggat_bayar today
        $barangs = Barang::whereDate('tenggat_bayar', $today)->get();

        foreach ($barangs as $barang) {
            // Check if notification already exists
            $exists = CustomNotification::where('type', 'hutang')
                ->where('id_data', $barang->id_barang)
                ->exists();

            if (!$exists) {
                // Create notification
                CustomNotification::create([
                    'type' => 'hutang',
                    'id_data' => $barang->id_barang,
                    'icon' => 'fas fa-exclamation-triangle text-white',
                    'message' => "Barang {$barang->nama_barang} jatuh tempo pembayaran pada hari ini.",
                ]);
            }
        }
        Log::info('Job CheckTenggatWaktuJob finished at: ' . now());
    }
}
