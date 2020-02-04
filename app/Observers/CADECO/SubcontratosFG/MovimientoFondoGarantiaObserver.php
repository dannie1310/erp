<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:29 PM
 */

namespace App\Observers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\MovimientoFondoGarantia;

class MovimientoFondoGarantiaObserver
{
    /**
     * @param MovimientoFondoGarantia $movimientoFondoGarantia
     */
    public function creating(MovimientoFondoGarantia $movimientoFondoGarantia)
    {
        $movimientoFondoGarantia->created_at = date('Y-m-d h:i:s');
        $movimientoFondoGarantia->usuario_registra = auth()->id();
        $movimientoFondoGarantia->importe = ($movimientoFondoGarantia->tipo->naturaleza == 2)? abs($movimientoFondoGarantia->importe) * -1 : $movimientoFondoGarantia->importe;
    }

    public function created(MovimientoFondoGarantia $movimientoFondoGarantia)
    {
        $movimientoFondoGarantia->fondo_garantia->actualizaSaldo();
    }
}
