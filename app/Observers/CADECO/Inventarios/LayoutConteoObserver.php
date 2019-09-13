<?php


namespace App\Observers\CADECO\Inventarios;


use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Models\CADECO\Inventarios\LayoutConteo;

class LayoutConteoObserver
{
    public function creating(LayoutConteo $layoutConteo){
        $inventario = InventarioFisico::query()->where('estado','=',0)->first();
        $layoutConteo->validar();
        $layoutConteo->usuario_carga = auth()->id();
        $layoutConteo->fecha_hora_carga = date('Y-m-d h:i:s');
        $layoutConteo->id_inventario_fisico = $inventario->id;
    }

    public function created(LayoutConteo $layoutConteo){
        $inventario = InventarioFisico::query()->where('estado','=',0)->first();
        $layoutConteo->validar();
        $layoutConteo->usuario_carga = auth()->id();
        $layoutConteo->fecha_hora_carga = date('Y-m-d h:i:s');
        $layoutConteo->id_inventario_fisico = $inventario->id;
    }
}