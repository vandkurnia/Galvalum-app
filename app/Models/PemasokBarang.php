<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class PemasokBarang extends Model
{
    use HasFactory;
    protected $table = 'pemasok_barangs';
    protected $primaryKey = 'id_pemasok';
    protected $fillable = [
        'hash_id_pemasok',
        'nama_pemasok',
        'no_telp_pemasok',
        'alamat_pemasok',
    ];

    public function Barang()
    {
        return $this->hasMany(Barang::class, 'id_pemasok');
    }

    protected static function booted()
    {
        static::creating(function ($pemasokBarang) {
            $pemasokBarang->hash_id_pemasok = Uuid::uuid4()->toString();
        });
    }
}
