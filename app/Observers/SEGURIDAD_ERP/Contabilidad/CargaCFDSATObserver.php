<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 28/02/2020
 * Time: 05:58 PM
 */

namespace App\Observers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;

use DateTime;

class CargaCFDSATObserver
{
    /**
     * @param CargaCFDSAT $log
     */
    public function creating(CargaCFDSAT $log)
    {
        $log->usuario_cargo = auth()->id();
        $log->fecha_hora_inicio = date("Y-m-d H:i:s");
    }

    /**
     * @param CargaCFDSAT $log
     */
    public function updating(CargaCFDSAT $log)
    {
        $log->usuario_cargo = auth()->id();
        $datetime1 = new DateTime($log->fecha_hora_inicio);
        $datetime2 = new DateTime($log->fecha_hora_fin);
        $duracion = $datetime1->diff($datetime2);
        $log->duracion = $duracion->format('%s');
    }

}