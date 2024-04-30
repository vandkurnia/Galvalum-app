<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukubesarBarangModel extends Model
{
    use HasFactory;
    protected $table = 'bukubesar_barang';

    protected $fillable = [
        'id_bukubesar',
        'id_barang'
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
