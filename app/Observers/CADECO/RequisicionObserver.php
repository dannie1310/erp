<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/11/2019
 * Time: 05:33 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Compras\RequisicionEliminada;
use App\Models\CADECO\Requisicion;
use App\Models\CADECO\Transaccion;

class RequisicionObserver extends TransaccionObserver
{
    /**
     * @param Requisicion $requisicion
     */
    public function creating(Transaccion $requisicion)
    {
        parent::creating($requisicion);
        $requisicion->tipo_transaccion = 16;
        $requisicion->estado = 0;
        $requisicion->opciones = 1;
    }

    public function deleting(Requisicion $requisicion)
    {
        $requisicion->eliminar_partidas();
        if($requisicion->complemento)
        {
            $requisicion->complemento->delete();
        }

        RequisicionEliminada::create(
            [
                'id_transaccion' => $requisicion->id_transaccion,
                'tipo_transaccion' => $requisicion->tipo_transaccion,
                'numero_folio' => $requisicion->numero_folio,
                'fecha' => $requisicion->fecha,
                'estado' => $requisicion->estado,
                'opciones' => $requisicion->opciones,
                'id_obra' => $requisicion->id_obra,
                'id_empresa' => $requisicion->id_empresa,
                'comentario' => $requisicion->comentario,
                'observaciones' => $requisicion->observaciones,
                'FechaHoraRegistro' => $requisicion->FechaHoraRegistro,
                'id_usuario' => $requisicion->id_usuario,
                'id_area_compradora' => $requisicion->complemento ? $requisicion->complemento->id_area_compradora : '',
                'id_tipo' => $requisicion->complemento ? $requisicion->complemento->id_tipo : '',
                'id_area_solicitante' => $requisicion->complemento ? $requisicion->complemento->id_area_solicitante : '',
                'folio_compuesto' => $requisicion->complemento ? $requisicion->complemento->folio_compuesto : '',
                'concepto' => $requisicion->complemento ? $requisicion->complemento->concepto : '',
                'registro' => $requisicion->complemento ? $requisicion->complemento->registro : '',
                'timestamp_registro' => $requisicion->complemento ? $requisicion->complemento->timestamp_registro : '',
                'motivo_eliminacion' => ''
            ]
        );
    }

    public function deleted(Requisicion $requisicion)
    {
        if($requisicion->activoFijo)
        {
            $requisicion->activoFijo->delete();
        }
    }
}