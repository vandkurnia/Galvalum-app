<?php

namespace App\Models\Retur;

use App\Models\PemasokBarang;
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
        'faktur_retur_pemasok',
        'tanggal_retur',
        'bukti_retur_pemasok',
        'jenis_retur',
        'total_nilai_retur',
        'pengembalian_data',
        'kekurangan',
        'status',
        'id_pemasok',
    ];

    public function pemasok()
    {
        return $this->belongsTo(PemasokBarang::class, 'id_pemasok');
    }
    protected static function booted()
    {
        static::creating(function ($returPemasok) {
            $returPemasok->hash_id_retur_pemasok = Uuid::uuid4()->toString();
        });
    }
}
