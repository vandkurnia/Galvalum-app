<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notabukubesar extends Model
{
    use HasFactory;
    protected $table = 'nota_bukubesar';
    protected $primaryKey = ['id_nota', 'id_bukubesar'];
    public $incrementing = false;
    protected $fillable = ['id_nota', 'id_bukubesar'];

    public function nota_pembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota', 'id_nota');
    }

    public function bukubesar()
    {
        return $this->belongsTo(BukubesarModel::class, 'id_bukubesar', 'id_bukubesar');
    }
}
