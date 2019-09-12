<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:16 PM
 */

namespace App\Observers\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\UsuarioAreaSubcontratante;

class UsuarioAreaSubcontratanteObserver
{
    /**
     * @param UsuarioAreaSubcontratante $subcontratante
     */
    public function creating(UsuarioAreaSubcontratante $subcontratante)
    {
        $subcontratante->registro = auth()->id();
        $subcontratante->timestamp_registro = date('Y-m-d h:i:s');
    }
}