<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:11 PM
 */

namespace App\Observers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\AuditoriaRolUsuario;

class AuditoriaRolUsuarioObserver
{
    /**
     * @param AuditoriaRolUsuario $rolUsuario
     */
    public function creating(AuditoriaRolUsuario $rolUsuario)
    {
        $rolUsuario->usuario_registro = auth()->id();
        $rolUsuario->created_at = date('Y-m-d h:i:s');
    }
}