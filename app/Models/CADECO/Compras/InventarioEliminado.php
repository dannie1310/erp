<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 06:08 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class InventarioEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.inventarios_eliminados';
    protected $primaryKey = 'id_lote';

    public $timestamps = false;
}