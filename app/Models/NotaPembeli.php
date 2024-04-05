<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaPembeli extends Model
{
    use HasFactory;

    protected $table = 'nota_pembelis';
    protected $primaryKey = 'id_nota';
    protected $fillable = [
        'jenis_pembelian',
        'status_pembelian',
        'id_pembeli',
        'id_admin',
    ];

    public function Pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }

    public function Admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function PesananPembeli()
    {
        return $this->hasMany(PesananPembeli::class, 'id_nota');
    }
}
