<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaPembeli extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nota_pembelis';
    protected $primaryKey = 'id_nota';
    protected $fillable = [
        'no_nota',
        'id_pembeli',
        'id_admin',
        'metode_pembayaran',
        'status_pembayaran',
        'sub_total',
        'diskon',
        'pajak',
        'total'
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
    // protected static function booted()
    // {
    //     static::creating(function ($notaPembeli) {
    //         $lastId = static::max('id');
    //         $lastId = $lastId ? $lastId : 0; // handle jika tabel kosong
    //         $lastId++;

    //         $notaPembeli->no_nota = 'NT' . date('Y') . date('mdHis') . str_pad($lastId, 4, '0', STR_PAD_LEFT);
    //     });
    // }
}
