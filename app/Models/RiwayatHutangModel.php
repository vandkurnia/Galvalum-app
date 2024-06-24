<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatHutangModel extends Model
{
    use HasFactory;
    protected $table = 'riwayat_hutang';

    protected $fillable = [
        'id_bukubesar',
        'id_barang',
        'nominal_dibayar'
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
