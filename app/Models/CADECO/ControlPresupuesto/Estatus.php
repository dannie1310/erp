<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 12:20 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.estatus';
    protected $primaryKey = 'id';
}