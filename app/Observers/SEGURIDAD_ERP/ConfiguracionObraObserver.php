<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:13 PM
 */

namespace App\Observers\SEGURIDAD_ERP;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;

class ConfiguracionObraObserver
{
    /**
     * @param ConfiguracionObra $configuracionObra
     */
    public function creating(ConfiguracionObra $configuracionObra)
    {
        $configuracionObra->id_user =  auth()->id();
        $configuracionObra->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
        $configuracionObra->id_obra = Context::getIdObra();
    }
}
