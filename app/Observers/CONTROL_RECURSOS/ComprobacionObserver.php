<?php

namespace App\Observers\CONTROL_RECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Models\IGH\Usuario;

class ComprobacionObserver
{
    /**
     * @param Comprobacion $comprobacion
     */
    public function creating(Comprobacion $comprobacion)
    {
        $folio =  $comprobacion->getNumeroFolio();
        $comprobacion->Serie = 'REL';
        $comprobacion->Folio = $folio;
        $usuario = Usuario::where('idusuario',auth()->id())->first();
        $creo = $usuario->nombre_completo. "|" . date("d-m-Y") . "|" . date("H:i:s", time());
        $comprobacion->Creo = $creo;
    }
}
