<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:37 PM
 */

namespace App\Observers\CADECO\SubcontratosFG;


use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;
use App\Models\CADECO\Estimacion;

class RetencionFondoGarantiaObserver
{
    /**
     * @param RetencionFondoGarantia $retencionFondoGarantia
     * @throws \Exception
     */
    public function creating(RetencionFondoGarantia $retencionFondoGarantia)
    {
        $retencionFondoGarantia->created_at = date('Y-m-d h:i:s');
        $estimacion = Estimacion::find($retencionFondoGarantia->id_estimacion);
        if(!(float) $estimacion->retencion>0){
            throw New \Exception('La retención de fondo de garantía establecida en la estimacion no es mayor a 0, la retención no puede generarse');
        }
    }

    public function created(RetencionFondoGarantia $retencionFondoGarantia)
    {
        $retencionFondoGarantia->generaMovimientoRegistro();
    }
}