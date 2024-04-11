<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'barangs';
    protected $primaryKey = 'id_barang';
    protected $fillable = [
        'hash_id_barang',
        'nama_barang',
        'harga_barang',
        'stok',
        'ukuran',
        'id_pemasok',
        'id_tipe_barang',
    ];

    public function Pemasok()
    {
        return $this->belongsTo(PemasokBarang::class, 'id_pemasok');
    }

    public function TipeBarang()
    {
        return $this->belongsTo(TipeBarang::class, 'id_tipe_barang');
    }

    public function PesananPembeli()
    {
        return $this->hasMany(PesananPembeli::class, 'id_barang');
    }
    protected static function booted()
    {
        static::creating(function ($stokbarang) {
            $stokbarang->hash_id_barang = Uuid::uuid4()->toString();
        });
    }
}
