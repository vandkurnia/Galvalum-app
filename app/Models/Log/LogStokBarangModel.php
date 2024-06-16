<?php

namespace App\Models\Log;

use App\Models\Barang;
use App\Models\StokBarangHistoryModel;
use App\Models\StokBarangModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogStokBarangModel extends Model
{
    use HasFactory;

    // Nama tabel yang sesuai
    protected $table = 'log_stok_barang';

    // Properti yang dapat diisi (mass assignable)
    protected $fillable = [
        'json_content',
        'tipe_log',
        'keterangan',
        'id_admin',
        'id_stok_barang',
        'id_barang',
        'id_stok_barang_history'
    ];

    // Atribut yang harus dikonversi ke array saat diakses
    protected $casts = [
        'json_content' => 'array',
    ];
    // Definisikan relasi dengan model User (admin)
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    // Definisikan relasi dengan model StokBarang
    // public function stokBarang()
    // {
    //     return $this->belongsTo(StokBarangModel::class, 'id_stok_barang');
    // }

    // Definisikan relasi dengan model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // Relasi dengan model StokBarangHistoryModel
    public function stokBarangHistory()
    {
        return $this->belongsTo(StokBarangHistoryModel::class, 'id_stok_barang_history', 'id_stok');
    }
}
