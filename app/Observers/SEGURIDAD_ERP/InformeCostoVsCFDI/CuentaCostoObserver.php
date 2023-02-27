<?php

namespace App\Observers\SEGURIDAD_ERP\InformeCostoVsCFDI;

use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCosto;

class CuentaCostoObserver
{
    /**
     * @param CuentaCostoObserver $cuenta_costo
     * @return void
     */
    public function creating(CuentaCosto $cuenta_costo)
    {
        $cuenta_costo->usuario_registro = auth()->id();
        $cuenta_costo->fecha_hora_registro = date('Y-m-d h:i:s');
    }

    public function updating(CuentaCosto $cuenta_costo)
    {
        if($cuenta_costo->estatus != $cuenta_costo->getOriginal('estatus') && $cuenta_costo->estatus == 0)
        {
            $cuenta_costo->usuario_elimino = auth()->id();
            $cuenta_costo->fecha_hora_eliminacion = date('Y-m-d h:i:s');
        }
    }
}
