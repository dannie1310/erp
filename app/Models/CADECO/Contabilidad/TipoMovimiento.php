<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 07:32 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.tipos_movimientos';
}