<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

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
    protected static function booted()
    {
        static::creating(function ($pembeli) {
            $pembeli->hash_id_pembeli = Uuid::uuid4()->toString();
        });
    }
}
