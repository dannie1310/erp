<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class LogEdicion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.LogEdicion';
    protected $primaryKey = 'id';
    protected $fillable =[
        "id_poliza",
        "id_empresa",
        "id_cuenta",
        "empresa",
        "id_movimiento",
        "id_campo",
        "valor_original",
        "valor_modificado",
        "usuario_modifico",
        "id_solicitud_partida",
    ];
    public $timestamps = false;
}