<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 11:44 AM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class TipoRubro extends Model
{
    use SoftDeletes;
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.tipos_rubros';
}