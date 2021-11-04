<?php


namespace App\Observers\CADECO;

use App\Models\CADECO\Contrato;
use App\Models\CADECO\Contratos\ContratoEliminado;
use App\Models\CADECO\Contratos\DestinoEliminado;

class ContratoObserver
{
    /**
     * @param Contrato $contrato
     * @throws \Exception
     *
     */
    public function created(Contrato $contrato)
    {
        $contrato->registrarDestino();
    }

    public function updated(Contrato $contrato)
    {
        $contrato->editarDestino();
    }

    public function deleting(Contrato $contrato)
    {
        ContratoEliminado::create([
            'id_transaccion' => $contrato->id_transaccion,
            'id_concepto' => $contrato->id_concepto,
            'nivel' => $contrato->nivel,
            'descripcion' => $contrato->descripcion,
            'id_destino' => $contrato->id_destino,
            'unidad' => $contrato->unidad,
            'cantidad_original' => $contrato->cantidad_original,
            'cantidad_presupuestada' => $contrato->cantidad_presupuestada,
            'cantidad_modificada' => $contrato->cantidad_modificada,
            'estado' => $contrato->estado,
            'clave' => $contrato->clave,
            'id_marca' => $contrato->id_marca,
            'id_modelo' => $contrato->id_modelo,
            'usuario_elimina' => auth()->id(),
            'fecha_eliminacion' => date('Y-m-d H:i:s')
        ]);

        $destino=$contrato->destino;

        if($destino){
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
}
