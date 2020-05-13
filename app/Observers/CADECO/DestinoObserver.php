<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Contratos\DestinoEliminado;
use App\Models\CADECO\Destino;

class DestinoObserver
{
    public function deleting(Destino $destino)
    {
        DestinoEliminado::create([
            'id_transaccion' => $destino->id_transaccion,
            'id_concepto_contrato' => $destino->id_concepto_contrato,
            'id_concepto' => $destino->id_concepto,
            'id_concepto_original' => $destino->id_concepto_original,
            'usuario_elimina' => $destino->usuario_elimina,
            'fecha_eliminacion' => $destino->fecha_eliminacion,
            'usuario_elimina' => auth()->id(),
            'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);
    }
}
