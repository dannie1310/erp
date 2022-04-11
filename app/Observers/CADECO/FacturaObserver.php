<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:46 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoFactura;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Finanzas\FacturaEliminada;

class FacturaObserver extends TransaccionObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Transaccion $factura)
    {
        parent::creating($factura);
        $factura->tipo_transaccion = 65;
        $factura->opciones = 0;
    }

    public function updating(Factura $factura){
        if($factura->saldo<-1)
        {
            throw New \Exception('La  factura '.$this->numero_folio_format.' con referencia "'.$this->referencia.'" tiene saldo '.$factura->saldo.' no puede ser menor a 0');
        }
    }

    public function deleting(Factura $factura)
    {
        $factura->validarEstado();
        $factura->validarOrigen();
        $factura->validarEliminacion();
        $factura->desvincularPolizas();
        $factura->desvinculaFacturaRepositorio();
        FacturaEliminada::create([
            'id_transaccion' => $factura->id_transaccion,
            'tipo_transaccion' => $factura->tipo_transaccion,
            'numero_folio' => $factura->numero_folio,
            'fecha' => $factura->fecha,
            'vencimiento' => $factura->vencimiento,
            'estado' => $factura->estado,
            'id_obra' => $factura->id_obra,
            'id_empresa' => $factura->id_empresa,
            'monto' => $factura->monto,
            'saldo' => $factura->saldo,
            'observaciones' => $factura->observaciones,
            'FechaHoraRegistro' => $factura->FechaHoraRegistro,
            'id_usuario_registro' => $factura->id_usuario,

        ]);
    }
}
