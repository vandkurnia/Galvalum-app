<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class AkunBayarModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'akun_bayar';
    protected $primaryKey = 'id_akunbayar';

    protected $fillable = [
        'id_akunbayar',
        'hash_id_akunbayar',
        'no_akun',
        'nama_akun',
        'tipe_akun',
        'saldo',
        'saldo_akhir',
        'saldo_anak',
    ];
     protected static function booted()
    {
        static::creating(function ($akunbayar) {
            $akunbayar->hash_id_akunbayar = Uuid::uuid4()->toString();
        });
    }
    public function bukubesar()
    {
        return $this->hasMany(BukubesarModel::class, 'id_akunbayar');
    }
   
}
