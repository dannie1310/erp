<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:38 PM
 */

namespace App\Observers\CADECO\Seguridad;


use App\Models\CADECO\Seguridad\AuditoriaPermisoRol;

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