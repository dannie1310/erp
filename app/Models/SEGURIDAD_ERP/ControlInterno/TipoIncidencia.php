<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/01/2020
 * Time: 09:44 PM
 */

namespace App\Models\SEGURIDAD_ERP\ControlInterno;

use Illuminate\Database\Eloquent\Model;
class TipoIncidencia extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'ControlInterno.ctg_tipos_incidencias';
    public $timestamps = false;

}