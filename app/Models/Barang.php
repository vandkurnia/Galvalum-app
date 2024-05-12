<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Barang extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'barangs';
    protected $primaryKey = 'id_barang';
    protected $fillable = [
        'hash_id_barang',
        'kode_barang',
        'nama_barang',
        'harga_barang',
        'harga_barang_pemasok',
        'stok',
        'ukuran',
        // 'status_pembayaran',
        'total',
        'nominal_terbayar',
        'tenggat_bayar',
        'id_pemasok',
        'id_tipe_barang',
    ];



    public function bukuBesar()
    {
        return $this->belongsToMany(BukubesarModel::class, 'bukubesar_barang', 'id_barang', 'id_bukubesar');
    }
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


    public function stokBarang()
    {
        return $this->hasMany(StokBarangModel::class, 'id_barang');
    }
    protected static function booted()
    {
        static::creating(function ($stokbarang) {
            $stokbarang->hash_id_barang = Uuid::uuid4()->toString();
        });
    }
}
