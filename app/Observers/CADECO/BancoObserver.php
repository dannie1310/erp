<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:55 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Banco;

class BancoObserver
{
    /**
     * @param Banco $banco
     */
    public function creating(Banco $banco)
    {
        $banco->tipo_empresa = 8;
        $banco->UsuarioRegistro = auth()->id();
        $banco->razon_social = mb_strtoupper($banco->razon_social);
        $banco->FechaHoraRegistro = date('Y-m-d h:i:s');
    }
}