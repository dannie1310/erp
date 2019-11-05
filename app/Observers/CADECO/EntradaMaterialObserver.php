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

    public function deleting(EntradaMaterial $entradaMaterial)
    {
        $items = $entradaMaterial->partidas()->get()->toArray();
        $entradaMaterial->eliminar_partidas($items);
        $entradaMaterial->liberarOrdenCompra();
    }
}