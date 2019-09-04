<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/09/2019
 * Time: 03:48 PM
 */

namespace App\Observers\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;

class ConfiguracionEstimacionObserver
{

    /**
     * @param ConfiguracionEstimacion $configuracionEstimacion
     */
    public function creating(ConfiguracionEstimacion $configuracionEstimacion)
    {
        $configuracionEstimacion->validar();
        $configuracionEstimacion->id_obra =  Context::getIdObra();
        $configuracionEstimacion->usuario_crea = auth()->id();
        $configuracionEstimacion->usuario_actualiza = auth()->id();
        $configuracionEstimacion->created_at = date('Y-m-d h:i:s');
        $configuracionEstimacion->updated_at = date('Y-m-d h:i:s');
    }
}