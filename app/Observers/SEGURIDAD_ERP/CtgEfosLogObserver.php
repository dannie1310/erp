<?php


namespace App\Observers\SEGURIDAD_ERP;

use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfosLog;

class CtgEfosLogObserver
{
    /**
     * @param CtgEfosLog $log
     */

     public function creating(CtgEfosLog $log)
     {
            $log->timestamp_registro = date('Y-m-d h:i:s');
            $log->usuario = auth()->user()->usuario;
         
     }

     public function created(CtgEfosLog $log)
     {
        $log->validarRegistros();
     }

}