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
        $telefono->validar();
        $telefono->estatus = 1;
        $telefono->registro = auth()->id();
    }

    public function updating(Telefono $telefono){
        $telefono->validar();
    }
}
