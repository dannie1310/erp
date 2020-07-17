<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 04/06/2020
 * Time: 12:25 p. m.
 */


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CtgFormaPagoCredito extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.ctg_formas_pago_credito';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
