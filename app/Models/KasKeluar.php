<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class KasKeluar extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'kas_keluar';
    protected $primaryKey = 'id_kas_keluar';
    protected $fillable = [
        'nama_pengeluaran',
        'deskripsi',
        'jumlah_pengeluaran',
        'tanggal',
    ];
}
