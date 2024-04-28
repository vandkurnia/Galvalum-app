<?php

namespace App\Models\Retur;

use App\Models\PesananPembeli;
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
    ];

    public function returPembeli()
    {
        return $this->belongsTo(ReturPembeliModel::class, 'id_retur_pembeli');
    }

    public function pesananPembeli()
    {
        return $this->belongsTo(PesananPembeli::class, 'id_pesanan_pembeli');
    }
}
