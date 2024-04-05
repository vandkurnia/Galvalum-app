<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;
    protected $table = 'returs';
    protected $primaryKey = 'id_retur';
    protected $fillable = [
        'tanggal_retur',
        'bukti',
        'jenis_retur',
        'keterangan',
        'id_pesanan',
    ];

    public function PesananPembeli()
    {
        return $this->belongsTo(PesananPembeli::class, 'id_pesanan');
    }
}
