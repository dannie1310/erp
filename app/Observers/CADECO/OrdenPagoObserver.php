<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:43 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\OrdenPago;

class OrdenPagoObserver
{
    /**
     * @param OrdenPago $ordenPago
     * @throws \Exception
     */
    public function creating(OrdenPago $ordenPago)
    {
        if (!$ordenPago->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $ordenPago->tipo_transaccion = 68;
        $ordenPago->opciones = 0;
        $ordenPago->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $ordenPago->FechaHoraRegistro = date('Y-m-d h:i:s');
        $ordenPago->id_obra = Context::getIdObra();
        $ordenPago->id_usuario = auth()->id();
    }
}
