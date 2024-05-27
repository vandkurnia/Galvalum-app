<?php

namespace App\Models;

use App\Models\Retur\ReturPembeliModel;
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
        'sub_total',
        'nominal_terbayar',
        'tenggat_bayar',
        'diskon',
        'ongkir',
        'total',
        'piutang_is_visible'
    ];

    // public function bukuBesar()
    // {
    //     return $this->belongsTo(BukubesarModel::class, 'id_bukubesar');
    // }

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
    public function bukuBesar()
    {
        return $this->belongsToMany(BukubesarModel::class, 'nota_bukubesar', 'id_nota', 'id_bukubesar');
    }

    // Relasi dengan ReturPembeli berdasarkan id_nota
    public function returPembelis()
    {
        return $this->hasMany(ReturPembeliModel::class, 'id_nota', 'id_nota');
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
