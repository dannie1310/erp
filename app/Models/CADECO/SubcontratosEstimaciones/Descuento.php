<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 03:18 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.descuento';
    protected $primaryKey = 'id_descuento';
}