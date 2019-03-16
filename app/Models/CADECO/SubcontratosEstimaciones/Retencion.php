<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 03:57 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use Illuminate\Database\Eloquent\Model;

class Retencion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.retencion';
    protected $primaryKey = 'id_retencion';
    public $timestamps = false;
}