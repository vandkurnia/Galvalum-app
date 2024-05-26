<?php

namespace App\Models\pdf;

use App\Models\NotaPembeli;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuratJalanModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_jalan';
    protected $primaryKey = 'id_surat_jalan';

    protected $fillable = [
        'no_surat_jalan',
        'users',
        'id_nota',
        // tambahkan kolom lain yang ada di tabel jika diperlukan
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users', 'id_admin');
    }

    public function notaPembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota', 'id_nota');
    }
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


            // Validate uniqueness of no_surat_jalan
            $existingSuratJalan = DB::table('surat_jalan')
                ->where('no_surat_jalan', $suratjalan->no_surat_jalan)
                ->exists();

            if ($existingSuratJalan) {
                throw ValidationException::withMessages(['no_surat_jalan' => 'The generated no_surat_jalan must be unique.']);
            }
        });
    }
}
