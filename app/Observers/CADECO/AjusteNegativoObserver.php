<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 06:11 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\AjusteNegativo;

class AjusteNegativoObserver
{
    /**
     * @param AjusteNegativo $ajusteNegativo
     * @throws \Exception
     */
    public function creating(AjusteNegativo $ajusteNegativo)
    {
        if (!$ajusteNegativo->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $ajusteNegativo->tipo_transaccion = 35;
        $ajusteNegativo->opciones = 1;
        $ajusteNegativo->estado = 0;
        $ajusteNegativo->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $ajusteNegativo->FechaHoraRegistro = date('Y-m-d h:i:s');
        $ajusteNegativo->id_obra = Context::getIdObra();
        $ajusteNegativo->fecha = date('Y-m-d h:i:s');
        $ajusteNegativo->id_usuario = auth()->id();
    }
}