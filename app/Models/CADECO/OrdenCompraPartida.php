<?php
/**
 * Created by PhpStorm.
 * User: Luis M.Valencia
 * Date: 01/07/2018
 * Time: 11:29 AM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Compras\OrdenCompraPartidaComplemento;
use App\Models\CADECO\Item;


class OrdenCompraPartida extends Item
{
    public  function orden_partida_complemento(){
        return $this->hasOne(OrdenCompraPartidaComplemento::class, 'id_item');
    }
}