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
        $model->fecha = date('Y-m-d h:i:s');
        $model->FechaHoraRegistro = date('Y-m-d h:i:s');
        $model->usuario_elimina = auth()->id();
        $model->id_obra = Context::getIdObra();
        $model->fecha_eliminacion = date('Y-m-d h:i:s');
    }

}
