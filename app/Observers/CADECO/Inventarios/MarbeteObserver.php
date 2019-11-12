<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/11/2019
 * Time: 12:11 PM
 */

namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\Marbete;

class MarbeteObserver
{

    /**
     * @param Marbete $marbete
     */
    public function creating(Marbete $marbete)
    {
        $marbete->precio_unitario = $marbete->precioUnitarioPromedio();
    }
}