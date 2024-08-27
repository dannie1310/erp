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
use Carbon\Carbon;
class FacturaRepositorioObserver
{
    public function creating(FacturaRepositorio $factura)
    {
        $factura->usuario_registro = auth()->id();
        if ($factura->id_documento_cr == null && $factura->id_doc_relacion_gastos_cr == null) {dd("AQUI");
            $factura->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
            $factura->id_obra = Context::getIdObra();
            if ($factura->id_transaccion > 0) {
                $factura->usuario_asocio = auth()->id();
                $factura->fecha_hora_asociacion = Carbon::now();
            }
        } else {
            $factura->usuario_asocio = auth()->id();
            $factura->fecha_hora_asociacion = Carbon::now();
        }
    }

    public function updating(FacturaRepositorio $factura)
    {
        if ($factura->getOriginal("id_transaccion") == null && $factura->id_transaccion > 0) {
            $factura->usuario_asocio = auth()->id();
            $factura->fecha_hora_asociacion = Carbon::now();
            $factura->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
            $factura->id_obra = Context::getIdObra();
        }

        if ($factura->getOriginal("id_documento_cr") == null && $factura->id_documento_cr > 0) {
            $factura->usuario_asocio = auth()->id();
            $factura->fecha_hora_asociacion = Carbon::now();
        }

        if ($factura->getOriginal("id_doc_relacion_gastos_cr") == null && $factura->id_doc_relacion_gastos_cr > 0) {
            $factura->usuario_asocio = auth()->id();
            $factura->fecha_hora_asociacion = Carbon::now();
        }
    }
}
