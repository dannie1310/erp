<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:48 PM
 */

namespace App\Observers\CADECO\Seguridad;

use App\Facades\Context;
use App\Models\CADECO\Seguridad\Rol;
use App\Utils\Normalizar;

class RolObserver
{
    public function creating(Rol $rol)
    {
        $name = Normalizar::normaliza($rol->display_name);
        $rol->name = str_replace(' ', '_', $name);
        $rol->id_obra = Context::getIdObra();
    }
}