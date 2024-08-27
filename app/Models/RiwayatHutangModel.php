<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatHutangModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'riwayat_hutang';

    protected $fillable = [
        'id_bukubesar',
        'id_barang',
        'nominal_dibayar',
        'hutang_dan_stok_id'
    ];

    public function bukubesar()
    {
        return $this->belongsTo(BukubesarModel::class, 'id_bukubesar');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
