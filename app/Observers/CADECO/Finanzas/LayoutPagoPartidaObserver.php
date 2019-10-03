<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/10/2019
 * Time: 07:45 PM
 */

namespace App\Observers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\LayoutPagoPartida;

class LayoutPagoPartidaObserver
{
    /**
     * @param LayoutPagoPartida $partida
     */
    public function creating(LayoutPagoPartida $partida)
    {
        $partida->validarRegistro();
    }
}