<?php

namespace App\Observers\ACARREOS;

use App\Models\ACARREOS\Operador;

class OperadorObserver
{
    /**
     * @param Origen $origen
     */
    public function creating(Operador $operador)
    {
        $operador->validarRegistro();
        $operador->FechaAlta = date('Y-m-d');
        $operador->Estatus = 1;
        $operador->usuario_registro = auth()->id();
        
    }

    public function updating(Operador $operador)
    {
      if($operador->isDirty('Nombre')){
         $operador->validaRegistroNombre();
      }
      if($operador->isDirty('NoLicencia')){
         $operador->validaRegistroLicencia();
      }
    }
}
