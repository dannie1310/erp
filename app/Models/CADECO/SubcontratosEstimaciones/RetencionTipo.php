<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 10/02/2020
 * Time: 06:17 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;

use Illuminate\Database\Eloquent\Model;

class RetencionTipo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.retencion_tipo';
    protected $primaryKey = 'id_tipo_retencion';
    public $timestamps = false;

}