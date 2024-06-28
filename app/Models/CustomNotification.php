<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_notifikasi';
    protected $table = 'custom_notifications';

    protected $fillable = [
        'type', 'id_data', 'icon', 'message'
    ];
}
