<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HutangDanStokModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lacak_stok';
    protected $primaryKey = 'id_hutang_stok';
    protected $fillable = [
        'harga_beli',
        'total',
        'dp',
        'nominal_terbayar',
        'tenggat_waktu',
        'stok',
        'id_bukubesar',
        'id_barang',
        'hidden'
    ];

    public function bukubesar()
    {
        return $this->belongsTo(BukubesarModel::class, 'id_bukubesar');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
    public function riwayatHutang()
    {
        return $this->hasMany(RiwayatHutangModel::class, 'hutang_dan_stok_id', 'id_hutang_stok');
    }
}
