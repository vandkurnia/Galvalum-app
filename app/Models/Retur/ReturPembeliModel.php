<?php

namespace App\Models\Retur;

use App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class ReturPembeliModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'retur_pembeli';
    protected $primaryKey = 'id_retur_pembeli';

    protected $fillable = [
        'hash_id_retur_pembeli',
        'no_retur_pembeli',
        'faktur_retur_pembeli',
        'tanggal_retur_pembeli',
        'bukti_retur_pembeli',
        'jenis_retur',
        'total_nilai_retur',
        'pengembalian_data',
        'kekurangan',
        'status',
        'id_pembeli',
    ];
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }
    protected static function booted()
    {
        static::creating(function ($returPembeli) {
            $returPembeli->hash_id_retur_pembeli = Uuid::uuid4()->toString();
        });
    }
}
