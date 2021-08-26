<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class RoleUserGlobal extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'role_user_global';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
    ];
}
