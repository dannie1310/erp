<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 31/10/2019
 * Time: 12:25 p. m.
 */


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoSolicitud extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.ctg_estados_solicitud';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
