<?php

namespace App\Models\pdf;

use App\Models\NotaPembeli;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaranModel extends Model
{
    use HasFactory;

    protected $table = 'invoice_pembelian';

    protected $fillable = [
        'users',
        'id_nota',
        // tambahkan kolom lain yang ada di tabel jika diperlukan
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users', 'id_admin');
    }

    public function notaPembeli()
    {
        return $this->belongsTo(NotaPembeli::class, 'id_nota', 'id_nota');
    }
}
