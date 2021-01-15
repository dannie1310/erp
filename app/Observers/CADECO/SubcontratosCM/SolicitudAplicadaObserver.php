<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:59 PM
 */

namespace App\Observers\CADECO\SubcontratosCM;

use App\Models\CADECO\SubcontratosCM\SolicitudAplicada;


class SolicitudAplicadaObserver
{
    public function creating(SolicitudAplicada $solicitudAplicada)
    {
        $solicitudAplicada->fecha_aplicacion = date('Y-m-d H:i:s');
        $solicitudAplicada->usuario_aplico = auth()->id();
    }
}
