<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:07 PM
 */

namespace App\Observers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\AuditoriaPermisoRol;

class AuditoriaPermisoRolObserver
{
    /**
     * @param AuditoriaPermisoRol $auditoriaPermisoRol
     */
    public function creating(AuditoriaPermisoRol $auditoriaPermisoRol)
    {
        $auditoriaPermisoRol->usuario_registro = auth()->id();
        $auditoriaPermisoRol->created_at = date('Y-m-d h:i:s');
    }
}