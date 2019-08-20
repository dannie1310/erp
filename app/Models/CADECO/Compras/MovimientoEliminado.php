<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 06:10 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class MovimientoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.movimientos_eliminados';
    protected $primaryKey = 'id_movimiento';

    public $timestamps = false;
}