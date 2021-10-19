<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Transaccion;

class AvanceObraObserver extends TransaccionObserver
{
    public function creating(Transaccion $avance)
    {
        parent::creating($avance);
        $avance->tipo_transaccion = 98;
        $avance->opciones = 1;
    }
}
