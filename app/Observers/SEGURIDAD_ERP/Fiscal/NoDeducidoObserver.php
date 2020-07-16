<?php


namespace App\Observers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\NoDeducido;

class NoDeducidoObserver
{
    /**
     * @param NoDeducido $noDeducido
     */
    public function creating(NoDeducido $noDeducido)
    {
        $noDeducido->fecha_hora_registro = date('Y-m-d H:i:s');
        $noDeducido->usuario_registro = auth()->id();
        $noDeducido->estado = 7;
    }
}
