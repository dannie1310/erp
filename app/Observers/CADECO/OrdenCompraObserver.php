<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:46 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Compras\OrdenCompraEliminada;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Transaccion;

class OrdenCompraObserver extends TransaccionObserver
{
    /**
     * @param OrdenCompra $ordenCompra
     */
    public function creating(Transaccion $ordenCompra)
    {
       parent::creating($ordenCompra);
        $ordenCompra->fecha = date('Y-m-d');
        $ordenCompra->tipo_transaccion = 19;
        $ordenCompra->opciones = 1;

    }

    public function deleting(OrdenCompra $ordenCompra)
    {

        OrdenCompraEliminada::create([
            'id_transaccion' => $ordenCompra->id_transaccion,
            'id_antecedente' => $ordenCompra->id_antecedente,
            'id_referente' => $ordenCompra->id_referente,
            'tipo_transaccion' => $ordenCompra->tipo_transaccion,
            'id_obra' => $ordenCompra->id_obra,
            'id_empresa' => $ordenCompra->id_empresa,
            'id_sucursal' => $ordenCompra->id_sucursal,
            'id_moneda' => $ordenCompra->id_moneda,
            'opciones' => $ordenCompra->opciones,
            'observaciones' => $ordenCompra->observaciones,
            'fecha' => $ordenCompra->fecha,
            'comentario' => $ordenCompra->comentario,
            'FechaHoraRegistro' => $ordenCompra->FechaHoraRegistro,
            'numero_folio' => $ordenCompra->numero_folio,
            'monto' => $ordenCompra->monto,
            'saldo' => $ordenCompra->saldo,
            'impuesto' => $ordenCompra->impuesto,
            'anticipo_monto' => $ordenCompra->anticipo_monto,
            'anticipo_saldo' => $ordenCompra->anticipo_saldo,
            'porcentaje_anticipo_pactado' => $ordenCompra->porcentaje_anticipo_pactado,
            'id_costo' => $ordenCompra->id_costo,
            'idserie' => $ordenCompra->complemento->idserie?$ordenCompra->complemento->idserie:0,
            'idrqctoc_tabla_comparativa' => $ordenCompra->complemento->idrqctoc_tabla_comparativa?$ordenCompra->complemento->idrqctoc_tabla_comparativa:0,
            'plazos_entrega_ejecucion' => $ordenCompra->complemento->plazos_entrega_ejecucion,
            'timestamp_registro' => $ordenCompra->complemento->timestamp_registro,
            'registro' => $ordenCompra->complemento->registro,
            'estatus' => $ordenCompra->complemento->estatus,
            'id_forma_pago' => $ordenCompra->complemento->id_forma_pago,
            'id_forma_pago_credito' => $ordenCompra->complemento->id_forma_pago_credito,
            'id_tipo_credito' => $ordenCompra->complemento->id_tipo_credito,
            'domicilio_entrega' => $ordenCompra->complemento->domicilio_entrega,
            'otras_condiciones' => $ordenCompra->complemento->otras_condiciones,
            'fecha_entrega' => $ordenCompra->complemento->fecha_entrega,
            'con_fianza' => $ordenCompra->complemento->con_fianza?$ordenCompra->complemento->con_fianza:0,
            'id_tipo_fianza' => $ordenCompra->complemento->id_tipo_fianza,
            'elimino' => auth()->id(),
        ]);

        foreach ($ordenCompra->partidas()->get() as $item) {
            $item->delete();
        }

        if($ordenCompra->complemento)
        {
            $ordenCompra->complemento->asignacion->estado = 1;
            $ordenCompra->complemento->asignacion->save();
            $ordenCompra->complemento->delete();
        }
    }
}
