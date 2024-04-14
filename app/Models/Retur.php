<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retur extends Model
{
    use HasFactory;
    use SoftDeletes;

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
