<?php


namespace App\Observers\ACARREOS;

use App\Models\ACARREOS\Material;



class MaterialObserver
{
    /**
     * @param Material $material
     */
    public function creating(Material $material)
    {
        $material->validarRegistro();
        $material->usuario_registro = auth()->id();
        $material->IdProyecto = 1;
        $material->Estatus = 1;
    }
}
