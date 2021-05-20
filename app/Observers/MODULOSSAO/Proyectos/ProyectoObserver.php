<?php

namespace App\Observers\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Proyectos\Proyecto;

class ProyectoObserver
{
    /**
     * @param Proyecto $poliza
     */

    public function updated(Proyecto $proyecto)
    {
        if($proyecto->getOriginal("CantidadExtraordinariasPermitidas") !=  $proyecto->CantidadExtraordinariasPermitidas){
            $proyecto->logs()->create([
                "IDTipoAutorizacion"=>1,
                "ValorOriginal"=>$proyecto->getOriginal("CantidadExtraordinariasPermitidas"),
                "ValorAutorizado"=>$proyecto->CantidadExtraordinariasPermitidas,
                "UsuarioAutorizo"=>auth()->id()
            ]);
        }
    }

}
