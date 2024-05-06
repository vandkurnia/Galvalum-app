<?php

namespace App\Models;

use App\Models\Retur\ReturPembeliModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Pembeli extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pembelis';
    protected $primaryKey = 'id_pembeli';
    protected $fillable = [
        'nama_pembeli',
        'alamat_pembeli',
        'no_hp_pembeli',
        'jenis_pembeli'
    ];

    public function NotaPembelian()
    {
        return $this->hasMany(NotaPembeli::class, 'id_pembeli');
    }
    public function returPembeli()
    {
        return $this->hasMany(ReturPembeliModel::class, 'id_pembeli');
    }
    protected static function booted()
    {
        static::creating(function ($pembeli) {
            $pembeli->hash_id_pembeli = Uuid::uuid4()->toString();
        });
    }
}
