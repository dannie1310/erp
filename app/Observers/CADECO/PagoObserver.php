<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:46 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Pago;

class PagoObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Pago $pago)
    {
         if (!$pago->validaTipoAntecedente()) {
                throw New \Exception('La transacción antecedente no es válida');
         }
        $pago->tipo_transaccion = 82;
        $pago->opciones = 0;
        $pago->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $pago->FechaHoraRegistro = date('Y-m-d h:i:s');
        $pago->id_obra = Context::getIdObra();
    }
}
