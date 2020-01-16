<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:11 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Empresa;

class EmpresaObserver
{
    /**
     * @param Empresa $empresa
     */
    public function creating(Empresa $empresa)
    {
        $empresa->validaRFC($empresa);
        $empresa->razon_social = mb_strtoupper($empresa->razon_social);
        $empresa->FechaHoraRegistro = date('Y-m-d H:i:s');
        $empresa->UsuarioRegistro = auth()->id();
    }

    public function updating(Empresa $empresa)
    {
        $empresa->validaRFC($empresa);
    }

    public function deleting(Empresa $empresa)
    {
        $empresa->validaEliminacion();
    }
}
