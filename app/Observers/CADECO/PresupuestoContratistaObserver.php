<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\PresupuestoContratista;

class PresupuestoContratistaObserver
{
    /**
     * @param PresupuestoContratista $presupuestoContratista
     */

     public function updating(PresupuestoContratista $presupuestoContratista)
     {
         $presupuestoContratista->validarAsignacion('editar');
     }
}