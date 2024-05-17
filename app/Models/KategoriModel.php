<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class KategoriModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    protected $increment = true;
    protected $fillable = [
        'nama_kategori',
    ];


    protected static function booted()
    {
        static::creating(function ($kategori) {
            $kategori->hash_id_kategori = Uuid::uuid4()->toString();
        });
    }
    public function bukubesar()
    {
        return $this->hasMany(BukubesarModel::class, 'id_kategori', 'id_kategori');
    }
}
