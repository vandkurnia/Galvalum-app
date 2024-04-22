<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class DiskonModel extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'diskon';
    protected $primaryKey = 'id_diskon';
 
    protected $fillable = [
        'hash_id_diskon',
        'kode_diskon',
        'nama_diskon',
        'type',
        'besaran',
        'status',
    ];
    protected static function booted() 
    {
        static::creating(function ($diskon) {
            $diskon->hash_id_diskon = Uuid::uuid4()->toString();

        });

    }
}
