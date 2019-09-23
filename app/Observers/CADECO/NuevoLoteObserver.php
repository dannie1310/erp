<?php


namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\AjusteNegativo;
use App\Models\CADECO\NuevoLote;

class NuevoLoteObserver
{
    public function creating(NuevoLote $nuevo_lote)
    {
        if (!$nuevo_lote->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $nuevo_lote->tipo_transaccion = 35;
        $nuevo_lote->opciones = 2;
        $nuevo_lote->estado = 0;
        $nuevo_lote->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $nuevo_lote->FechaHoraRegistro = date('Y-m-d h:i:s');
        $nuevo_lote->id_obra = Context::getIdObra();
        $nuevo_lote->fecha = date('Y-m-d h:i:s');
        $nuevo_lote->id_usuario = auth()->id();
    }

}
