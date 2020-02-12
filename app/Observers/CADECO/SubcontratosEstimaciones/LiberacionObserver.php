<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 11/02/2020
 * Time: 20:05 PM
 */

namespace App\Observers\CADECO\subcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;

class LiberacionObserver {
    /**
     * @param Liberacion $liberacion
     * @throws \Exception
     */
    public function creating(Liberacion $liberacion)
    {
        $liberacion->usuario = auth()->user()->usuario;
    }
}