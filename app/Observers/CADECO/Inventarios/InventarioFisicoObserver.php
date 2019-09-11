<?php


namespace App\Observers\CADECO\Inventarios;


use App\Facades\Context;
use App\Models\CADECO\Inventarios\InventarioFisico;

class InventarioFisicoObserver
{
    public function creating(InventarioFisico $inventario)
    {
        $inventario->id_obra = Context::getIdObra();
        $inventario->usuario_inicia = auth()->id();
        $inventario->fecha_hora_inicio =  date('Y-m-d h:i:s');
    }

}