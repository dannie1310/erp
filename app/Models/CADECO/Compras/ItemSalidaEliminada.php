<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 06:00 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class ItemSalidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.items_salidas_eliminadas';
    protected $primaryKey = 'id_item';

    public $timestamps = false;
}