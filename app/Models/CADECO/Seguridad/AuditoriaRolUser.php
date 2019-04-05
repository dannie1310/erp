<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:28
 */

namespace App\Models\CADECO\Seguridad;

use App\Facades\Context;
use App\Models\CADECO\Seguridad\Rol;
use App\Models\SEGURIDAD_ERP\Permiso;
use Illuminate\Database\Eloquent\Model;

class AuditoriaRolUser extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Seguridad.auditoria_role_user';

    protected $fillable = [
        'usuario_registro',
        'action'
    ];


    public function roles()
    {
        return $this->belongsToMany(Rol::class, Context::getDatabase() . '.Seguridad.roles', 'id', 'role_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, Context::getDatabase() . '.dbo.permissions', 'id', 'permission_id');
    }
}