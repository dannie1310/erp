<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/06/2020
 * Time: 01:02 PM
 */

namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class CtgTipoSolicitudEdicion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.ctg_tipos_solicitudes_edicion';

}