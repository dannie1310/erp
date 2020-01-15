<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Destajista;
use App\Models\CADECO\Empresa;

class DestajistaObserver extends EmpresaObserver
{
    /**
     * @param Destajista $destajista
     */
    public function creating(Empresa $destajista)
    {
        parent::creating($destajista);
        $destajista->validaDuplicidadRfc();
        $destajista->tipo_empresa = 4;
    }

    public function updating(Empresa $destajista)
    {
        parent::updating($destajista);
        $destajista->validaDuplicidadRfc();
    }

    public function deleting(Empresa $destajista)
    {
        parent::deleting($destajista);
    }
}
