<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:20 PM
 */

namespace App\Observers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLog;

class DistribucionRecursoRemesaLogObserver
{
    /**
     * @param DistribucionRecursoRemesaLog $log
     */
    public function creating(DistribucionRecursoRemesaLog $log)
    {
        $log->id_usuario = auth()->id();
    }
}