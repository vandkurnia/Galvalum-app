<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananPembeli extends Model
{
    use HasFactory;
    protected $table = 'pesanan_pembelis';
    protected $primaryKey = 'id_pesanan';
    protected $fillable = [
        'jumlah_pembelian',
        'id_nota',
        'id_barang',
    ];

    public function NotaPembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota');
    }

    public function Barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
