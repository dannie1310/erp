<?php


namespace App\Observers\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaExcluidaDocumentacion;

class EmpresaExcluidaDocumentacionObserver
{
    /**
     * @param EmpresaExcluidaDocumentacion $empresaExcluidaDocumentacion
     */
    public function creating(EmpresaExcluidaDocumentacion $empresaExcluidaDocumentacion)
    {

    }
}
