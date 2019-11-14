<?php


namespace App\Observers\CADECO\Inventarios;


use App\Facades\Context;
use App\Models\CADECO\Inventarios\InventarioFisico;
use Illuminate\Support\Facades\DB;

class InventarioFisicoObserver
{
    public function creating(InventarioFisico $inventario)
    {
        try {
            $inventario->validar();
            $inventario->id_obra = Context::getIdObra();
            $inventario->usuario_inicia = auth()->id();
            $inventario->fecha_hora_inicio =  date('Y-m-d h:i:s');
            $inventario->folio =  $inventario->calcularFolio();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

}
