<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class AuditoriaPermisoRol extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.auditoria_permission_role';
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id',
        'action',
        'created_at'
    ];
}
