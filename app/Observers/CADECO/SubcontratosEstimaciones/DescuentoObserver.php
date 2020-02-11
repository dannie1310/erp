<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 12:33 PM
 */

namespace App\Observers\CADECO\subcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Descuento;



class DescuentoObserver {
    /**
     * @param Descuento $descuento
     * @throws \Exception
     */
    public function updating(Descuento $descuento)
    {
        $descuento->validar_cantidad($descuento->toArray());
    }
}