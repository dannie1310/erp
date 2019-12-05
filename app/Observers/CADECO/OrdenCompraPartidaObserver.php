<?php
/**
 * Created by PhpStorm.
 * User: Emartinez
 * Date: 19/11/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\OrdenCompraPartida;

class OrdenCompraPartidaObserver
{
    public function updating(OrdenCompraPartida $partida)
    {
        if($partida->saldo<-0.01)
        {
            throw New \Exception('El saldo de la partida de la orden de compra de material'.$partida->material->descripcion.' no puede ser menor a 0, por favor solicite una revisión al área de soporte a aplicaciones');
        }
    }
}