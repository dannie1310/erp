<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:02 PM
 */

namespace App\Models\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\Rol as RolGlobal;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Seguridad\Rol as RolPersonalizado;

class Permiso extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'permissions';
    /*protected $dateFormat = 'Y-m-d H:i:s';*/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sistema()
    {
        return $this->belongsTo(Sistema::class, 'sistema_id');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeReservados($query)
    {
        return $query->where('reservado', '=', true);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeReporte($query)
    {
        return $query->where('permissions.name', 'like', 'reporte_%');
    }

    public function roles()
    {
        $obra =  Obra::query()->find(Context::getIdObra());

        if ($obra->configuracion) {
            if ($obra->configuracion->esquema_permisos == 1) {
                // Esquema Global
                return $this->belongsToMany(RolGlobal::class, 'dbo.permission_role', 'permission_id', 'role_id');
            } else if ($obra->configuracion->esquema_permisos == 2) {
                // Esquema Personalizado
                return $this->belongsToMany(RolPersonalizado::class,Context::getDatabase() . '.Seguridad.permission_role','permission_id','role_id');
            }
        } else {
            // Esquema Global
            return $this->belongsToMany(RolGlobal::class, 'dbo.permission_role', 'permission_id', 'role_id');
        }
    }

    public function scopePorRol($query, $role_id)
    {
        return $query->whereHas('roles', function ($q) use ($role_id){
            return $q->where('id', '=', $role_id);
        });
    }
}
