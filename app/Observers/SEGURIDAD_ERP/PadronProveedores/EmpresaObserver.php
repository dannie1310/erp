<?php


namespace App\Observers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;

class EmpresaObserver
{
    /**
     * @param Empresa $empresa
     */
    public function creating(Empresa $empresa)
    {
        $empresa->usuario_registro = auth()->id();
        $empresa->estatus = 1;
        $empresa->id_estado_expediente = 1;
    }
}
