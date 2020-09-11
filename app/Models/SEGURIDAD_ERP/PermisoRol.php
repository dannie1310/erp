<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class PermisoRol extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'permission_role';
    public $timestamps = false;

    protected $fillable = [
        'permission_id',
        'role_id'
    ];
}
