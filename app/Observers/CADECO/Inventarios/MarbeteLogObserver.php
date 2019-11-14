<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/11/2019
 * Time: 06:04 PM
 */

namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\MarbeteLog;

class MarbeteLogObserver
{

    /**
     * @param MarbeteLog $log
     */
    public function creating(MarbeteLog $log)
    {
        $log->usuario = auth()->id();
        $log->fecha_hora = date('Y-m-d H:i:s');
        $log->estado = 0;
    }
}