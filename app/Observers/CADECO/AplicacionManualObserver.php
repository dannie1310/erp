<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\AplicacionManual;
use App\Models\CADECO\Transaccion;

class AplicacionManualObserver extends TransaccionObserver
{
    /**
     * @param AplicacionManual $aplicacion
     * @throws \Exception
     */
    public function creating(Transaccion $aplicacion)
    {
        parent::creating($aplicacion);
        $aplicacion->tipo_transaccion = 70;
        $aplicacion->opciones = 0;
        $aplicacion->estado = 1;
    }

    public function deleting(AplicacionManual $aplicacion)
    {
        parent::creating($aplicacion);
    }
}
