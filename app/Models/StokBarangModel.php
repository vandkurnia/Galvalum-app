<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokBarangModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'stok_barang';
    protected $fillable = ['id_barang', 'id_bukubesar', 'stok_masuk', 'stok_keluar'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
