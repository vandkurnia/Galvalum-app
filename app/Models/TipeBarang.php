<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeBarang extends Model
{
    use HasFactory;
    protected $table = 'tipe_barangs';
    protected $primaryKey = 'id_tipe_barang';
    protected $fillable = [
        'nama_tipe',
    ];

    public function Barang()
    {
        return $this->hasMany(Barang::class, 'id_tipe_barang');
    }
}
