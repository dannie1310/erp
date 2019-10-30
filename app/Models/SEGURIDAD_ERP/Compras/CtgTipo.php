<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time: 05:46 PM
 */


namespace App\Models\SEGURIDAD_ERP\Compras;

use Illuminate\Database\Eloquent\Model;

class CtgTipo extends Model
{
    protected $connection = 'seguridad';
    protected $table ='Compras.ctg_tipos';
    protected $primaryKey = 'id';
    public $timestamps= false;
}
