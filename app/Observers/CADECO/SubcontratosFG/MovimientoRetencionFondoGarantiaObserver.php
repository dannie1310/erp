<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:32 PM
 */

namespace App\Observers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\MovimientoRetencionFondoGarantia;

class MovimientoRetencionFondoGarantiaObserver
{
    /**
     * @param MovimientoRetencionFondoGarantia $movimiento
     */
    public function creating(MovimientoRetencionFondoGarantia $movimiento)
    {
        $movimiento->created_at = date('Y-m-d h:i:s');
        $movimiento->usuario_registra = auth()->id();
    }

    public function created(MovimientoRetencionFondoGarantia $movimiento)
    {
        if($movimiento->id_tipo_movimiento == 1) {
            $movimiento->retencion->estimacion->subcontrato->fondo_garantia->generaMovimientoRetencion($movimiento);
        }
    }
}
