<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/02/2020
 * Time: 06:41 PM
 */

namespace App\Observers\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;

class LogEdicionObserver
{
    /**
     * @param LogEdicion $log
     */
    public function creating(LogEdicion $log)
    {
        $log->usuario_modifico = auth()->id();
        $log->bd_contpaq = config('database.connections.cntpq.database');
        $log->fecha_hora = date("Y-m-d H:i:s");
    }
}
