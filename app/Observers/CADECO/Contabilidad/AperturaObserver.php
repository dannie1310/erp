<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:39 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Apertura;
use Carbon\Carbon;

class AperturaObserver
{
    /**
     * @param Apertura $apertura
     */
    public function creating(Apertura $apertura)
    {
        $apertura->estatus = 1;
        $apertura->registro = auth()->id();
        $apertura->inicio_apertura = Carbon::now()->toDateTimeString();
    }
}