<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:15 PM
 */

namespace App\Observers\CADECO\Contratos;


use App\Models\CADECO\Contratos\AreaSubcontratante;

class AreaSubcontratanteObserver
{
    /**
     * @param AreaSubcontratante $subcontratante
     */
    public function creating(AreaSubcontratante $subcontratante)
    {
        $subcontratante->id_usuario = auth()->id();
        $subcontratante->timestamp_registro = date('Y-m-d h:i:s');
    }
}