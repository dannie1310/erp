<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 04:51 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class EstatusPrepoliza extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.estatus_prepolizas';
    public $timestamps = false;
}