<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'role_user';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'id_obra',
        'id_proyecto'
    ];
}
