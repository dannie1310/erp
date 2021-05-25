<?php


namespace App\Observers\ACARREOS;

use App\Models\ACARREOS\Impresora;

class ImpresoraObserver
{
    public function creating(Impresora $impresora)
    {
        $impresora->validarRegistro();
        $impresora->registro = auth()->id();
        $impresora->estatus = 1;
    }
}
