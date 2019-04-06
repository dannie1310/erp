<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Garrido
 * Date: 04/04/2019
 * Time: 19:40
 */

namespace App\Models\CADECO\Seguridad;

use App\Facades\Context;
use App\Models\CADECO\Seguridad\Rol;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Permiso;

class AuditoriaPermisoRol extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'Seguridad.auditoria_permission_role';

    protected $fillable = [
        'usuario_registro',
        'action'
    ];

    public function roles()
    {
        return $this->belongsTo(Rol::class,'rol_id');
    }
    public function permisos()
    {
        return $this->belongsTo(Permiso::class, 'permission_id');
    }
}