<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\Marca;

class MarcaObserver
{
    /**
     * @param Marca $marca
     */
    public function creating(Marca $marca)
    {
        $marca->validarRegistro();
        $marca->usuario_registro = auth()->id();
        $marca->Estatus = 1;
    }
}
