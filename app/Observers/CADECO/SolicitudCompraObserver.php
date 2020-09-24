<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 01:03 p. m.
 */


namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Compras\SolicitudEliminada;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Transaccion;

class SolicitudCompraObserver extends TransaccionObserver
{
    /**
     * @param SolicitudCompra $solicitudCompra
     */

    public function creating(Transaccion $solicitudCompra)
    {
        parent::creating($solicitudCompra);

        $solicitudCompra->tipo_transaccion = 17;
        $solicitudCompra->estado = 0;
        $solicitudCompra->opciones = 1;
    }

    public function deleting(SolicitudCompra $solicitudCompra)
    {
        $solicitudCompra->eliminarPartidas();

        SolicitudEliminada::create(
            [
                'id_transaccion' => $solicitudCompra->id_transaccion,
                'tipo_transaccion' => $solicitudCompra->tipo_transaccion,
                'numero_folio' => $solicitudCompra->numero_folio,
                'fecha' => $solicitudCompra->fecha,
                'estado' => $solicitudCompra->estado,
                'id_obra' => $solicitudCompra->id_obra,
                'opciones' => $solicitudCompra->opciones,
                'comentario' => $solicitudCompra->comentario,
                'observaciones' => $solicitudCompra->observaciones,
                'FechaHoraRegistro' => $solicitudCompra->FechaHoraRegistro,
                'id_usuario' => $solicitudCompra->id_usuario,
                'id_area_compradora' => $solicitudCompra->complemento ? $solicitudCompra->complemento->id_area_compradora : NULL,
                'id_tipo' => $solicitudCompra->complemento ? $solicitudCompra->complemento->id_tipo : NULL,
                'id_area_solicitante' => $solicitudCompra->complemento ? $solicitudCompra->complemento->id_area_solicitante : NULL,
                'folio_compuesto' => $solicitudCompra->complemento ? $solicitudCompra->complemento->folio_compuesto : NULL,
                'concepto' => $solicitudCompra->complemento ? $solicitudCompra->complemento->concepto : NULL,
                'fecha_requisicion_origen' => $solicitudCompra->complemento ? $solicitudCompra->complemento->fecha_requisicion_origen : NULL,
                'requisicion_origen' => $solicitudCompra->complemento ? $solicitudCompra->complemento->requisicion_origen : NULL,
                'motivo' => '',
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')
            ]
        );

        if($solicitudCompra->complemento)
        {
            $solicitudCompra->complemento->delete();
        }
    }

    public function deleted(SolicitudCompra $solicitudCompra)
    {
        if ($solicitudCompra->activoFijo)
        {
            $solicitudCompra->activoFijo->delete();
        }
    }
}
