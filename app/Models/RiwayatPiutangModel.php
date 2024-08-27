<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatPiutangModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'riwayat_piutang';
    protected $primaryKey = 'id_piutang';
    protected $fillable = ['id_nota', 'id_bukubesar', 'nominal_dibayar'];

    public function notaPembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota', 'id_nota');
    }

    public function bukuBesar()
    {
        return $this->belongsTo(BukubesarModel::class, 'id_bukubesar', 'id_bukubesar');
    }
}
