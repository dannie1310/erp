<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:50 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\PagoVario;

class PagoVarioObserver
{
    /**
     * @param PagoVario $pagoVario
     * @throws \Exception
     */
    public function creating(PagoVario $pagoVario)
    {
        if (!$pagoVario->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $pagoVario->tipo_transaccion = 82;
        $pagoVario->opciones = 1;
        $pagoVario->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $pagoVario->FechaHoraRegistro = date('Y-m-d h:i:s');
        $pagoVario->id_obra = Context::getIdObra();
        $pagoVario->id_usuario = auth()->id();
    }
}
