<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\Contratos\PresupuestoContratistaEliminado;
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