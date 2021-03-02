<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\ComprobanteFondo;
use App\Models\CADECO\Transaccion;

class ComprobanteFondoObserver extends TransaccionObserver
{

    /**
     * @param ComprobanteFondo $comprobanteFondo
     * @throws \Exception
     */
    public function creating(Transaccion $comprobanteFondo)
    {
        parent::creating($comprobanteFondo);

        $comprobanteFondo->tipo_transaccion = 101;
        $comprobanteFondo->opciones = 0;
        $comprobanteFondo->id_moneda = 1;
    }
}
