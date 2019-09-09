<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:45 PM
 */

namespace App\Observers\CADECO\Seguridad;


use App\Models\CADECO\Seguridad\AuditoriaRolUser;

class AuditoriaRolUserObserver
{
    /**
     * @param AuditoriaRolUser $auditoria
     */
    public function creating(AuditoriaRolUser $auditoria)
    {
        $auditoria->usuario_registro = auth()->id();
        $auditoria->created_at = date('Y-m-d h:i:s');
    }
}