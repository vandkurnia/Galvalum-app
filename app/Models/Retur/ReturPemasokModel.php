<?php

namespace App\Models\Retur;

use App\Models\Barang;
use App\Models\PemasokBarang;
use App\Models\StokBarangModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class ReturPemasokModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'retur_pemasok';
    protected $primaryKey = 'id_retur_pemasok';

    protected $fillable = [
        'hash_id_retur_pemasok',
        'no_retur_pemasok',
        'tanggal_retur',
        'bukti_retur_pemasok',
        'jenis_retur',
        'total_nilai_retur',
        'pengembalian_data',
        'kekurangan',
        'harga',
        'total',
        'qty',
        'qty_sebelum_perubahan',
        'type_retur_pesanan',
        'status',
        'id_pemasok',
        'id_barang',
        'id_stok_barang'
    ];

    public function pemasok()
    {
        return $this->belongsTo(PemasokBarang::class, 'id_pemasok', 'id_pemasok');
    }

    protected static function booted()
    {
        static::creating(function ($returPemasok) {
            $returPemasok->hash_id_retur_pemasok = (string) Uuid::uuid4();
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function stokBarang()
    {
        return $this->belongsTo(StokBarangModel::class, 'id_stok_barang', 'id');
    }
}
