<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\AvanceObra;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\AvanceObra\AvanceObraEliminado;

class AvanceObraObserver extends TransaccionObserver
{
    public function creating(Transaccion $avance)
    {
        parent::creating($avance);
        $avance->tipo_transaccion = 98;
        $avance->opciones = 0;
    }

    public function deleting(AvanceObra $avanceObra)
    {
        $avanceObra->eliminarPartidas();
        AvanceObraEliminado::create([
            'id_transaccion' => $avanceObra->id_transaccion,
            'tipo_transaccion' => $avanceObra->tipo_transaccion,
            'numero_folio' => $avanceObra->numero_folio,
            'fecha' => $avanceObra->fecha,
            'estado' => $avanceObra->estado,
            'id_obra' => $avanceObra->id_obra,
            'id_concepto' => $avanceObra->id_concepto,
            'cumplimiento' => $avanceObra->cumplimiento,
            'vencimiento' => $avanceObra->vencimiento,
            'opciones' => $avanceObra->opciones,
            'monto' => $avanceObra->monto,
            'impuesto' => $avanceObra->impuesto,
            'comentario' => $avanceObra->comentario,
            'observaciones' => $avanceObra->observaciones,
            'FechaHoraRegistro' => $avanceObra->FechaHoraRegistro,
            'id_usuario' => $avanceObra->id_usuario,
            'motivo' => '',
            'usuario_elimina' => auth()->id(),
            'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);
    }
}
