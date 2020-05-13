<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Contratos\ContratoProyectadoEliminado;

class ContratoProyectadoObserver extends TransaccionObserver
{
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
    }
}
