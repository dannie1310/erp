<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:28 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EntradaMaterial;
use App\Models\CADECO\Transaccion;

class EntradaMaterialObserver extends TransaccionObserver
{
    /**
     * @param EntradaMaterial $entradaMaterial
     */
    public function creating(Transaccion $entradaMaterial)
    {
        parent::creating($entradaMaterial);
        $entradaMaterial->tipo_transaccion = 33;
        $entradaMaterial->estado = 0;
        $entradaMaterial->opciones = 1;
    }

    public function created(Transaccion $entradaMaterial)
    {
       $entradaMaterial->ordenCompra->update(["estado"=>1]);
    }

    public function deleting(EntradaMaterial $entradaMaterial)
    {
        $items = $entradaMaterial->partidas()->get()->toArray();
        $entradaMaterial->eliminar_partidas($items);
    }
    public function deleted(EntradaMaterial $entradaMaterial)
    {
        $ordenCompra =  $entradaMaterial->ordenCompra;
        $entradas_restantes = $ordenCompra->entradasAlmacen;
        if(count($entradas_restantes)==0){
            $ordenCompra->estado = 0;
            $ordenCompra->save();
        }
        else{
            $ordenCompra->estado = 1;
            $ordenCompra->save();
        }

    }
}