<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:59 PM
 */

namespace App\Observers\CADECO\SubcontratosCM;

use App\Models\CADECO\SubcontratosCM\SolicitudCambioSubcontratoComplemento;


class SolicitudCambioSubcontratoComplementoObserver
{
    public function creating(SolicitudCambioSubcontratoComplemento $solicitudAplicada)
    {
        $solicitudAplicada->fecha = date('Y-m-d H:i:s');
        $solicitudAplicada->id_usuario = auth()->id();
    }
}
