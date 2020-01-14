<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 06/01/2020
 * Time: 07:46 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Observers\CADECO\EmpresaObserver;

class ProveedorContratistaObserver extends EmpresaObserver
{
    /**
     * @param Empresa $provedor_contratista
     * @throws \Exception
     */
    public function creating(Empresa $provedor_contratista)
    {
        parent::creating($provedor_contratista);
    }
    /**
     * @param Empresa $provedor_contratista
     * @throws \Exception
     */
    public function updating(Empresa $provedor_contratista)
    {
        parent::creating($provedor_contratista);
    }
}
