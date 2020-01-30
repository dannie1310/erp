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
        $destajista->validaDuplicidadRfc($destajista);
        $destajista->tipo_empresa = 4;
        $destajista->dias_credito = $destajista->dias_credito == '' ? 0 : $destajista->dias_credito;
    }

    public function updating(Empresa $destajista)
    {
        parent::updating($destajista);
        $destajista->validaDuplicidadRfc($destajista);
    }

    public function deleting(Empresa $destajista)
    {
        parent::deleting($destajista);
    }
}
