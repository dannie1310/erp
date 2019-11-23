<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 05/11/2019
 * Time: 10:12 AM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Inventario;


class InventarioObserver
{
    /**
     * @param Inventario $inventario
     * @throws \Exception
     */
    public function updating(Inventario $inventario)
    {
        if($inventario->saldo<-0.01){
            throw New \Exception('El saldo del lote ('.$inventario->id_lote.') '.$inventario->material->descripcion.' no puede ser menor a 0');
        }
        if($inventario->saldo > ($inventario->cantidad)+0.01){
            throw New \Exception('El saldo del lote ('.$inventario->id_lote.') '.$inventario->material->descripcion.' no puede ser mayor a '. $inventario->cantidad);
        }
    }

    public function deleting(Inventario $inventario)
    {
        $inventario->respaldar();
    }
}