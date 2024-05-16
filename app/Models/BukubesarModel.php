<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class BukubesarModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bukubesar';
    protected $primaryKey = 'id_bukubesar';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'hash_id_bukubesar',
        'id_akunbayar',
        'tanggal',
        'kategori',
        'keterangan',
        'debit',
        'kredit',
    ];

    protected static function booted()
    {
        static::creating(function ($bukubesar) {
            $bukubesar->hash_id_bukubesar = Uuid::uuid4()->toString();
        });
    }
    // Relasi ke tabel akun_bayar
    public function akunBayar()
    {
        return $this->belongsTo(AkunBayarModel::class, 'id_akunbayar', 'id_akunbayar');
    }
    public function notaPembeli()
    {
        return $this->belongsToMany(NotaPembeli::class, 'nota_bukubesar', 'id_bukubesar', 'id_nota');
    }
    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'bukubesar_barang', 'id_bukubesar', 'id_barang');
    }
}
