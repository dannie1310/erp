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
use App\Models\CADECO\Transaccion;

class AjustePositivoObserver extends TransaccionObserver
{
    /**
     * @param AjustePositivo $ajustePositivo
     *  @throws \Exception
     */
    public function creating(Transaccion $ajustePositivo)
    {
        parent::creating($ajustePositivo);
        $ajustePositivo->tipo_transaccion = 35;
        $ajustePositivo->opciones = 0;
        $ajustePositivo->estado = 0;
        $ajustePositivo->fecha = date('Y-m-d H:i:s');
    }
}