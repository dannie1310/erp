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
        RequisicionEliminada::create(
            [
                'id_transaccion' => $requisicion->id_transaccion,
                'tipo_transaccion' => $requisicion->tipo_transaccion,
                'numero_folio' => $requisicion->numero_folio,
                'fecha' => $requisicion->fecha,
                'estado' => $requisicion->estado,
                'id_obra' => $requisicion->id_obra,
                'id_empresa' => $requisicion->id_empresa,
                'comentario' => $requisicion->comentario,
                'observaciones' => $requisicion->observaciones,
                'FechaHoraRegistro' => $requisicion->FechaHoraRegistro,
                'id_usuario' => $requisicion->id_usuario,
                'id_area_compradora' => $requisicion->complemento->id_area_compradora,
                'id_tipo' => $requisicion->complemento->id_tipo,
                'id_area_solicitante' => $requisicion->complemento->id_area_solicitante,
                'folio_compuesto' => $requisicion->complemento->folio_compuesto,
                'concepto' => $requisicion->complemento->concepto,
                'registro' => $requisicion->complemento->registro,
                'timestamp_registro' => $requisicion->complemento->timestamp_registro,
                'motivo_eliminacion' => ''
            ]
        );
    }

    public function deleted(EntradaMaterial $entradaMaterial)
    {
        $ordenCompra = $entradaMaterial->ordenCompra;
        $entradas_restantes = $ordenCompra->entradasAlmacen;
        if (count($entradas_restantes) == 0) {
            $ordenCompra->estado = 0;
            $ordenCompra->save();
        } else {
            $ordenCompra->estado = 1;
            $ordenCompra->save();
        }
    }
}