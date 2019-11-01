<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 22/10/2019
 * Time: 10:00 PM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Transaccion;

class AnticipoObserver extends TransaccionObserver
{
    /**
     * @param Transaccion $anticipo
     * @throws \Exception
     */
    public function creating(Transaccion $anticipo){
        parent::creating($anticipo);
        $anticipo->tipo_transaccion = 66;
        $anticipo->estado = 2;
    }
}
