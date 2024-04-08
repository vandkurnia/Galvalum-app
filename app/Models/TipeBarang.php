<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class TipeBarang extends Model
{
    use HasFactory;
    protected $table = 'tipe_barangs';
    protected $primaryKey = 'id_tipe_barang';
    protected $fillable = [
        'hash_id_tipe_barang',
        'nama_tipe',
    ];

    public function Barang()
    {
        return $this->hasMany(Barang::class, 'id_tipe_barang');
    }


    protected static function booted()
    {
        static::creating(function ($tipebarang) {
            $tipebarang->hash_id_tipe_barang = Uuid::uuid4()->toString();
        });
    }
}
