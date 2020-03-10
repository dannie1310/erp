<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:26 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class EmpresaSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.ListaEmpresasSAT';
    protected $primaryKey = 'id';
    public $timestamps = false;

}