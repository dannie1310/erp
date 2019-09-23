<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:03 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Transaccion;

class TransaccionObserver
{
    /**
     * @param Transaccion $transaccion
     * @throws \Exception
     */
    public function creating(Transaccion $transaccion)
    {
        if (!$transaccion->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $transaccion->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $transaccion->FechaHoraRegistro = date('Y-m-d h:i:s');
        $transaccion->id_obra = Context::getIdObra();
        $transaccion->id_usuario = auth()->id();
    }
}