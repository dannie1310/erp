<?php


namespace App\Observers\ACARREOS;

use App\Models\ACARREOS\Telefono;

class TelefonoObserver
{
    /**
     * @param Telefono $telefono
     */
    public function creating(Telefono $telefono)
    {
        $telefono->estatus = 1;
        $telefono->registro = auth()->id();
    }
}
