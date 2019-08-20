<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:51 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'inventarios';
    protected $primaryKey = 'id_lote';

    public $timestamps = false;
}