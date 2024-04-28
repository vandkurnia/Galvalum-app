<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesananPembeli extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'pesanan_pembelis';
    protected $primaryKey = 'id_pesanan';
    protected $fillable = [
        'jumlah_pembelian',
        'harga',
        'diskon',
        'id_nota',
        'id_barang',
        'id_jenis_pelanggan',
        'id_diskon',
    ];

    public function NotaPembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota');
    }

    public function Barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function jenisPelanggan()
    {
        return $this->belongsTo(JenisPelangganModel::class, 'id_jenis_pelanggan');
    }

    public function diskon()
    {
        return $this->belongsTo(DiskonModel::class, 'id_diskon');
    }
}
