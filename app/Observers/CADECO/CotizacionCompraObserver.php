<?php


namespace App\Observers\CADECO;
use App\Models\CADECO\Compras\CotizacionEliminada;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Transaccion;


class CotizacionCompraObserver extends TransaccionObserver
{
    /**
     * @param CotizacionCompra $cotizacionCompra
     */

    public function creating(Transaccion $cotizacionCompra)
    {
        parent::creating($cotizacionCompra);

        $cotizacionCompra->tipo_transaccion = 18;
        $cotizacionCompra->estado = 0;
        $cotizacionCompra->opciones = 1;
        $cotizacionCompra->id_moneda = 1;
    }

    public function updating(CotizacionCompra $cotizacionCompra)
    {
        $cotizacionCompra->validarAsignacion('editar');
        $cotizacionCompra->estado = 1;
    }

    public function deleting(CotizacionCompra $cotizacionCompra)
    {
        $cotizacionCompra->eliminarPartidas();

        CotizacionEliminada::create([
                'id_transaccion' => $cotizacionCompra->id_transaccion,
                'id_antecedente' => $cotizacionCompra->id_antecedente,
                'tipo_transaccion' => $cotizacionCompra->tipo_transaccion,
                'numero_folio' => $cotizacionCompra->numero_folio,
                'fecha' => $cotizacionCompra->fecha,
                'estado' => $cotizacionCompra->estado,
                'id_obra' => $cotizacionCompra->id_obra,
                'id_empresa' => $cotizacionCompra->id_empresa,
                'id_sucursal' => $cotizacionCompra->id_sucursal,
                'id_moneda' => $cotizacionCompra->id_moneda,
                'cumplimiento' => $cotizacionCompra->cumplimiento,
                'vencimiento' => $cotizacionCompra->vencimiento,
                'opciones' => $cotizacionCompra->opciones,
                'monto' => $cotizacionCompra->monto,
                'saldo' => $cotizacionCompra->saldo,
                'autorizado' => $cotizacionCompra->autorizado,
                'impuesto' => $cotizacionCompra->impuesto,
                'referencia'=> $cotizacionCompra->referencia,
                'comentario' => $cotizacionCompra->comentario,
                'observaciones' => $cotizacionCompra->observaciones,
                'FechaHoraRegistro' => $cotizacionCompra->FechaHoraRegistro,
                'porcentaje_anticipo_pactado' => $cotizacionCompra->porcentaje_anticipo_pactado,
                'id_usuario' => $cotizacionCompra->id_usuario,
                'parcialidades' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->parcialidades : NULL,
                'dias_credito' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->dias_credito : NULL,
                'vigencia' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->vigencia : NULL,
                'plazo_entrega' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->plazo_entrega : NULL,
                'descuento' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->descuento : NULL,
                'anticipo' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->anticipo : NULL,
                'importe' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->importe : NULL,
                'tc_usd' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->tc_usd : NULL,
                'tc_eur' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->tc_eur : NULL,
                'registro' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->registro : NULL,
                'timestamp_registro' => $cotizacionCompra->complemento ? $cotizacionCompra->complemento->timestamp_registro : NULL,
                'motivo' => '',
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);

        if($cotizacionCompra->complemento)
        {
            $cotizacionCompra->complemento->delete();
        }
    }
}
