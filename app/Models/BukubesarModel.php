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

    protected $fillable = [
        'hash_id_bukubesar',
        'id_akunbayar',
        'tanggal',
        'kategori',
        'sub_kategori',
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
    public function akunbayar()
    {
        return $this->belongsTo(AkunBayarModel::class, 'id_akunbayar');
    }
    public function notaPembeli()
    {
        return $this->belongsToMany(NotaPembeli::class, 'nota_bukubesar', 'id_bukubesar', 'id_nota');
    }

   
}
