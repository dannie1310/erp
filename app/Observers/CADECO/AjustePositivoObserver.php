<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 10/09/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\AjustePositivo;

class AjustePositivoObserver
{
    /**
     * @param AjustePositivo $ajustePositivo
     * @throws \Exception
     */
    public function creating(AjustePositivo $ajustePositivo)
    {
        if (!$ajustePositivo->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $ajustePositivo->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $ajustePositivo->FechaHoraRegistro = date('Y-m-d h:i:s');
        $ajustePositivo->id_obra = Context::getIdObra();
    }
}