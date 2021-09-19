<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\Contratos\PresupuestoContratistaEliminado;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\Transaccion;

class PresupuestoContratistaObserver extends TransaccionObserver
{
    /**
     * @param PresupuestoContratista $presupuestoContratista
     */

     public function creating(Transaccion $presupuestoContratista)
     {
         parent::creating($presupuestoContratista);

         $presupuestoContratista->tipo_transaccion = 50;
         $presupuestoContratista->id_moneda = 1;
     }

     public function updating(PresupuestoContratista $presupuestoContratista)
     {
        if($presupuestoContratista->getOriginal('estado') == $presupuestoContratista->estado)
        {
            $presupuestoContratista->validarAsignacion('editar');
        }
     }

     public function deleting(PresupuestoContratista $presupuestoContratista)
     {
        $presupuestoContratista->validarAsignacion('eliminar');
        $partidas = $presupuestoContratista->datosPartidas();

        PresupuestoContratistaEliminado::create([
            'id_transaccion' => $presupuestoContratista->id_transaccion,
            'id_contrato_proyectado' => $presupuestoContratista->id_antecedente,
            'id_sucursal' => $presupuestoContratista->id_sucursal,
            'id_empresa' => $presupuestoContratista->id_empresa,
            'monto' => $presupuestoContratista->monto + $presupuestoContratista->impuesto,
            'impuesto' => $presupuestoContratista->impuesto,
            'TcUSD' => $presupuestoContratista->TcUSD,
            'TcEuro' => $presupuestoContratista->TcEuro,
            'registro' => $presupuestoContratista->id_usuario,
            'partidas' => json_encode($partidas),
            'fecha_registro' => $presupuestoContratista->FechaHoraRegistro,
            'id_usuario_elimino' => auth()->id(),
        ]);
        $presupuestoContratista->partidas()->delete();
     }
}
