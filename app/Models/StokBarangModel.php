<?php

namespace App\Models;

use App\Models\Retur\ReturPesananPembeliModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokBarangModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'stok_barang';
    protected $fillable = ['id_barang',
    //  'id_bukubesar',
      'stok_masuk', 'stok_keluar'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // public function returPesananPembelis()
    // {
    //     return $this->hasMany(ReturPesananPembeliModel::class, 'id_stok_barang', 'id');
    // }

    // Relasi dengan model PesananPembeli (One-to-One)
    public function pesananPembeli()
    {
        return $this->belongsTo(PesananPembeli::class, 'id', 'id_stokbarang');
    }
    
}
