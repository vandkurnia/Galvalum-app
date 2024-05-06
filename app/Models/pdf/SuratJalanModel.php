<?php

namespace App\Models\pdf;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratJalanModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_jalan'; // Nama tabel di database

    protected $primaryKey = 'id_surat_jalan'; // Nama kolom primary key

    protected $fillable = [
        'no_surat_jalan',
        'id_nota'
    ];

    protected static function booted()
    {
        static::creating(function ($suratjalan) {
            // Ambil id terakhir hari ini dari database
            $lastIdToday = DB::table('surat_jalan')
                ->whereDate('created_at', now()->toDateString())
                ->max('id_surat_jalan');

            // Jika ada id terakhir hari ini, tambahkan 1, jika tidak, set id menjadi 1
            $nextId = $lastIdToday ? $lastIdToday + 1 : 1;

            // Format id dengan leading zero sepanjang 4 digit
            $nextIdFormatted = str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Set no_surat_jalan dengan format 'SRTJLN' + tanggal hari ini + id
            $suratjalan->no_surat_jalan = 'SRTJLN' . date('Ymd') . $nextIdFormatted;
        });
    }
}
