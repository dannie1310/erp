<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 22/04/2020
 * Time: 02:27 PM
 */

namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Models\CADECO\Compras\AsignacionProveedorEliminada;

class AsignacionProveedorObserver
{
    /**
     * @param AsignacionProveedor $asignacion_proveedor
     */
    public function creating(AsignacionProveedor $asignacion_proveedor)
    {
        $asignacion_proveedor->registro = auth()->id();
    }

    public function deleting(AsignacionProveedor $asignacion_proveedor)
    {
        $asignacion_proveedor->validaAsociadaOrdenCompra();
        $partidas = $asignacion_proveedor->datosPartidas();
        AsignacionProveedorEliminada::create([
            'id_asignacion' => $asignacion_proveedor->id,
            'id_solicitud' => $asignacion_proveedor->id_transaccion_solicitud,
            'id_empresa' => $partidas[0]['id_empresa'],
            'registro' => $asignacion_proveedor->registro,
            'fecha_registro' => $asignacion_proveedor->timestamp_registro,
            'id_usuario_elimino' => auth()->id(),
            'partidas' => json_encode($partidas)
        ]);
        foreach($asignacion_proveedor->partidas as $partida){
            $partida->delete();
        }
    }
}
