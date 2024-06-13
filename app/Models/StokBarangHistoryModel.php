<?php

namespace App\Models;

use App\Models\Log\LogStokBarangModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBarangHistoryModel extends Model
{
    use HasFactory;
    protected $table = 'stok_barang_history';
    protected $primaryKey = 'id_stok';
    protected $fillable= [
        'id_barang',
        'stok_masuk',
        'stok_keluar',
        'stok_terkini'

    ];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
       // Relasi dengan model LogStokBarang
       public function logStokBarang()
       {
           return $this->hasMany(LogStokBarangModel::class, 'id_stok_barang_history', 'id_stok');
       }
}
