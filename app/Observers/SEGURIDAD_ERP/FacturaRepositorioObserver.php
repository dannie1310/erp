<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 15/01/2020
 * Time: 10:42 PM
 */

namespace App\Observers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Proyecto;

class FacturaRepositorioObserver
{
    public function creating(FacturaRepositorio $factura)
    {
        $factura->usuario_registro =  auth()->id();
        $factura->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
        $factura->id_obra = Context::getIdObra();
    }

}