<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class ModalTambahanModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'modal_tambahan';
    protected $primaryKey = 'id_modal_tambahan';
    protected $fillable = [
        'jenis_modal_tambahan',
        'deskripsi',
        'jumlah_modal',
        'tanggal',
    ];
}
