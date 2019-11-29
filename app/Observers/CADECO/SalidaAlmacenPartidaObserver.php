<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/10/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EntradaMaterialPartida;
use App\Models\CADECO\Inventario;
use App\Models\CADECO\Movimiento;
use App\Models\CADECO\Compras\ItemEntradaEliminada;
use App\Models\CADECO\SalidaAlmacenPartida;

class SalidaAlmacenPartidaObserver
{
    public function created(SalidaAlmacenPartida $partida)
    {
        /**
         * Se implementa la logica del stored procedure sp_salida_material
         * */
        $partida->salidaMaterial();
        /*$partida->importe = $importe;*/
        if ($partida->id_almacen != null) {
            $partida->ajustarValoresConsumos();
        }
    }
}