<?php


namespace App\Observers\CADECO\Almacenes;


use App\Facades\Context;
use App\Models\CADECO\Almacenes\AjusteEliminado;

class AjusteEliminadoObserver
{
    /**
     * @param AjusteEliminado
     * @throws \Exception
     */
    public function creating(AjusteEliminado $model)
    {
        $model->usuario_elimina = auth()->id();
        $model->fecha_eliminacion = date('Y-m-d H:i:s');
    }
}
