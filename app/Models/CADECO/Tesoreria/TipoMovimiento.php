<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 07:53 PM
 */

namespace App\Models\CADECO\Tesoreria;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoMovimiento extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Tesoreria.tipos_movimientos';
    protected $primaryKey = 'id_tipo_movimiento';
}