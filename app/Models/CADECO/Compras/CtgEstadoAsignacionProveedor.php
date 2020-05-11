<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 22/04/2020
 * Time: 02:25 p. m.
 */


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoAsignacionProveedor extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.ctg_estado_asignacion_proveedores';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
