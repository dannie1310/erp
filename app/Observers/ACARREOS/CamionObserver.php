<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\Camion;

class CamionObserver
{
    /**
     * @param Camion $camion
     */
    public function updating(Camion $camion)
    {
        if($camion->CubicacionParaPago > 40)
        {
            abort(400, "La cubicación del camión no puede ser mayor a 40.");
        }
    }
}
