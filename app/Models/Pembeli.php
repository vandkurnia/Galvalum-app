<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    use HasFactory;
    protected $table = 'pembelis';
    protected $primaryKey = 'id_pembeli';
    protected $fillable = [
        'nama_pembeli',
        'alamat_pembeli',
        'no_hp_pembeli',
    ];

    public function NotaPembelian()
    {
        return $this->hasMany(NotaPembeli::class, 'id_pembeli');
    }
}
