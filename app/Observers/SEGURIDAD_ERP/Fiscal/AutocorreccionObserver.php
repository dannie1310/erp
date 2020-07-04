<?php


namespace App\Observers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\Autocorreccion;

class AutocorreccionObserver
{
    /**
     * @param Autocorreccion $autocorreccion
     */
    public function creating(Autocorreccion $autocorreccion)
    {
        $autocorreccion->fecha_hora_registro = date('Y-m-d H:i:s');
        $autocorreccion->usuario_registro = auth()->id();
        $autocorreccion->estado = 0;
    }
}
