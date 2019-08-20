<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:59 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class ItemEntradaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.items_entradas_eliminadas';
    protected $primaryKey = 'id_item';

    public $timestamps = false;
}