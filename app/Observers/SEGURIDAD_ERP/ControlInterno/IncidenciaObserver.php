<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/01/2020
 * Time: 08:24 PM
 */

namespace App\Observers\SEGURIDAD_ERP\ControlInterno;
use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;


use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;

class IncidenciaObserver
{
    /**
     * @param Transaccion $transaccion
     * @throws \Exception
     */
    public function creating(Incidencia $incidencia)
    {

        $incidencia->id_obra = Context::getIdObra();
        $incidencia->base_datos = Context::getDataBase();
        $incidencia->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
        $incidencia->obra = ConfiguracionObra::query()->where('id_proyecto', '=', $incidencia->id_proyecto)
            ->where('id_obra', '=', Context::getIdObra())
            ->first()->nombre;
        $incidencia->id_usuario = auth()->id();
    }

}