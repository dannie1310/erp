<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\Empresa;

class EmpresaObserver
{
    /**
     * @param Empresa $empresa
     */
    public function creating(Empresa $empresa)
    {
        $empresa->validarRegistro();
        $empresa->Estatus = 1;
        $empresa->usuario_registro = auth()->id();
    }
}
