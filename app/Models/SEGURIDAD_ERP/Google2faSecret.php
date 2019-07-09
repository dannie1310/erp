<?php


namespace App\Models\SEGURIDAD_ERP;

use Illuminate\Database\Eloquent\Model;

class Google2faSecret extends Model
{
    protected $connection = 'seguridad';
    protected $table      = 'google_2fa_secret';
    protected $hidden = ['secret', 'created_at', 'updated_at'];
    protected $fillable = [
        'id_user',
        'secret'
    ];
}