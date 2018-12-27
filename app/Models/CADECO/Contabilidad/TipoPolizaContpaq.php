<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/12/18
 * Time: 11:08 AM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class TipoPolizaContpaq extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_tipos_polizas_contpaq';
    protected $primaryKey = 'id_int_tipo_poliza_contpaq';
}
