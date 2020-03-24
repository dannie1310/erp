<?php


namespace App\Observers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;

class PenalizacionLiberacionObserver
{
    /**
     * @param Penalizacion $liberacion
     * @throws \Exception
     */

     public function creating(PenalizacionLiberacion $liberacion)
     {
         $liberacion->validarImporteTotalALiberar();
         $liberacion->validadEstadoEstimacion('registrada');
         $liberacion->usuario = auth()->user()->usuario;
     }

     public function created(PenalizacionLiberacion $liberacion)
     {
         if($liberacion->penalizacion->importe == $liberacion->suma_liberado_por_penalizacion)
         {
             $liberacion->cerrarPenalizacion();
         }
     }

     public function deleting(PenalizacionLiberacion $liberacion)
     {
         $liberacion->validadEstadoEstimacion('eliminada');
     }

     public function deleted(PenalizacionLiberacion $liberacion)
     {
         if($liberacion->penalizacion->estatus == 1)
         {
             $liberacion->abrirPenalizacion();
         }
     }
}