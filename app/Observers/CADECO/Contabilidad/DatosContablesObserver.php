<?php

namespace App\Observers\CADECO\Contabilidad;

use App\Models\CADECO\Contabilidad\DatosContables;

class DatosContablesObserver
{
    /**
     * @param DatosContables $datosContables
     */
    public function updated(DatosContables $datosContables)
    {
        $datosContables->configuracion->numero_obra_contpaq = $datosContables->NumobraContPaq;
        $datosContables->configuracion->alias_bd_contpaq = $datosContables->BDContPaq;
        $datosContables->configuracion->save();
    }
}
