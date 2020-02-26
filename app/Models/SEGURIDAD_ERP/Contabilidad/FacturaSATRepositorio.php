<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:27 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class FacturaSATRepositorio extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.repositorio_facturas_sat';
    public $timestamps = false;

}