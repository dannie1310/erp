<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:48 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\PagoACuenta;

class PagoACuentaObserver
{
    /**
     * @param PagoACuenta $pagoACuenta
     * @throws \Exception
     */
    public function creating(PagoACuenta $pagoACuenta)
    {
        if (!$pagoACuenta->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $pagoACuenta->tipo_transaccion = 82;
        $pagoACuenta->opciones = 327681;
        $pagoACuenta->fecha = date('Y-m-d');
        $pagoACuenta->cumplimiento =  date('Y-m-d');
        $pagoACuenta->vencimiento = date('Y-m-d');
        $pagoACuenta->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $pagoACuenta->FechaHoraRegistro = date('Y-m-d h:i:s');
        $pagoACuenta->id_obra = Context::getIdObra();
    }
}