<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:15 PM
 */

namespace App\Observers\SEGURIDAD_ERP;

use App\Utils\Normalizar;
use App\Models\SEGURIDAD_ERP\Rol;

class RolObserver
{
    /**
     * @param Rol $rol
     */
    public function creating(Rol $rol)
    {
        $name = Normalizar::normaliza($rol->display_name);
        $rol->name = str_replace(' ', '_', $name);
    }
}