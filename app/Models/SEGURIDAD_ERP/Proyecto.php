<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 10/12/18
 * Time: 06:27 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Facades\Context;
use App\Utils\Normalizar;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Sistema;


class Proyecto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'proyectos';
    public $timestamps = false;

    public function sistemas()
    {
        return $this->belongsToMany(Sistema::class,  'dbo.proyectos_sistemas', 'id_proyecto', 'id_sistema');
    }

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {

            return $query->join('dbo.proyectos_sistemas', 'proyectos.id','=','dbo.proyectos_sistemas.id_proyecto')
                            ->join('dbo.role_user', 'role_user.id_proyecto','=','proyectos.id')
                            ->join('dbo.configuracion_obra', function ($join) {
                                $join->on('role_user.id_obra', '=', 'configuracion_obra.id_obra')->on('configuracion_obra.id_proyecto', '=', 'proyectos.id');
                            })
                            ->join('dbo.roles', 'role_user.role_id','=','roles.id')
                            ->join('dbo.permission_role', 'permission_role.role_id','=','roles.id')
                            ->join('dbo.permissions', 'permission_role.permission_id','=','permissions.id')
                            ->leftJoin('dbo.sistemas_permisos', 'sistemas_permisos.permission_id','=','permissions.id')
                            ->rightJoin('dbo.sistemas', function ($join) {
                                $join->on('sistemas_permisos.sistema_id', '=', 'sistemas.id')->on('proyectos_sistemas.id_sistema', '=', 'sistemas.id');
                            })
                            ->join('dbo.vwUsuariosIntranet', 'role_user.user_id','=','vwUsuariosIntranet.idusuario')


                ->where('proyectos.id',50);
        });
    }


}