<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:08 PM
 */

namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgPlaza extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_plazas';
    public $timestamps = false;
}