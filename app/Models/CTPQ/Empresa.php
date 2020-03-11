<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:54 AM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.ListaEmpresas';
    protected $primaryKey = 'Id';
    public $timestamps = false;
}