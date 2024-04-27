<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notabukubesar extends Model
{
    use HasFactory;
    protected $table = 'nota_bukubesar';
    protected $primaryKey = 'id_notabukubesar';
    protected $fillable = ['id_nota', 'id_bukubesar'];

    public function notaPembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota', 'id_nota');
    }

    public function bukuBesar()
    {
        return $this->belongsTo(BukubesarModel::class, 'id_bukubesar', 'id_bukubesar');
    }
}
