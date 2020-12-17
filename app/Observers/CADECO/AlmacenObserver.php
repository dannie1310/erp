<?php


namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Almacen;

class AlmacenObserver
{
    public function creating(Almacen $almacen)
    {
        $almacen->id_obra = Context::getIdObra();
        $almacen->opciones = 0;
        $almacen->fecha_registro = date('Y-m-d H:i:s');
        $almacen->id_usuario = auth()->id();
    }
}
