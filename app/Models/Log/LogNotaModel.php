<?php

namespace App\Models\Log;

use App\Models\NotaPembeli;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogNotaModel extends Model
{
    use HasFactory;

    protected $table = 'log_nota';

    protected $fillable = [
        'json_content',
        'tipe_log',
        'keterangan',
        'id_admin',
        'id_nota',
    ];

    protected $casts = [
        'json_content' => 'array',
    ];
    // Jika Anda memiliki relasi ke model User (admin)
   
    // Relasi ke model User
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    // Relasi ke model NotaPembeli
    public function nota()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota');
    }
}