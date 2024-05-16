<?php

namespace App\Models\Retur;

use App\Models\PesananPembeli;
use App\Models\StokBarangModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPesananPembeliModel extends Model
{
    use HasFactory;
    protected $table = 'retur_pesanan_pembeli';
    protected $primaryKey = 'id_pesanan_retur';
    public $timestamps = true;

    protected $fillable = [
        'id_retur_pembeli',
        'id_pesanan_pembeli',
        'harga',
        'total',
        'qty',
        'qty_sebelum_perubahan',
        'type_retur_pesanan',
        'id_stok_barang',
    ];

    public function returPembeli()
    {
        return $this->belongsTo(ReturPembeliModel::class, 'id_retur_pembeli');
    }

    public function pesananPembeli()
    {
        return $this->belongsTo(PesananPembeli::class, 'id_pesanan_pembeli');
    }

    public function stokBarang()
    {
        return $this->belongsTo(StokBarangModel::class, 'id_stok_barang', 'id');
    }
}
