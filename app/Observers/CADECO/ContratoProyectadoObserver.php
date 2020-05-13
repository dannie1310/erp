<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:46 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Contratos\AreaSubcontratanteEliminada;
use App\Models\CADECO\Contratos\ContratoProyectadoEliminado;
use App\Models\CADECO\Transaccion;

class ContratoProyectadoObserver extends TransaccionObserver
{
    /**
     * @param ContratoProyectado $contratoProyectado
     */
    public function creating(Transaccion $contratoProyectado)
    {
        parent::creating($contratoProyectado);
        $contratoProyectado->tipo_transaccion = 49;
        $contratoProyectado->opciones = 1026;
    }

    public function deleting(ContratoProyectado $contratoProyectado)
    {
        $contratoProyectado->eliminarPartidas();

        ContratoProyectadoEliminado::create(
            [
                'id_transaccion' => $contratoProyectado->id_transaccion,
                'tipo_transaccion' => $contratoProyectado->tipo_transaccion,
                'numero_folio' => $contratoProyectado->numero_folio,
                'fecha' => $contratoProyectado->fecha,
                'estado' => $contratoProyectado->estado,
                'id_obra' => $contratoProyectado->id_obra,
                'cumplimiento' => $contratoProyectado->cumplimiento,
                'vencimiento' => $contratoProyectado->vencimiento,
                'opciones' => $contratoProyectado->opciones,
                'referencia' => $contratoProyectado->referencia,
                'comentario' => $contratoProyectado->comentario,
                'observaciones' => $contratoProyectado->observaciones,
                'FechaHoraRegistro' => $contratoProyectado->FechaHoraRegistro,
                'id_usuario' => $contratoProyectado->id_usuario,
                'motivo' => '',
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')
            ]
        );

        if($contratoProyectado->areaSubcontratante)
        {
            AreaSubcontratanteEliminada::create([
                'id_transaccion' => $contratoProyectado->areaSubcontratante->id_transaccion,
                'id_area_subcontratante' => $contratoProyectado->areaSubcontratante->id_area_subcontratante,
                'id_usuario' => $contratoProyectado->areaSubcontratante->id_usuario,
                'timestamp_registro' => $contratoProyectado->areaSubcontratante->timestamp_registro,
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')
            ]);

            $contratoProyectado->areaSubcontratante->delete();
        }
    }
}
