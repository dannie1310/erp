<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 08:05 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCuentaContable extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_tipos_cuentas_contables';
    protected $primaryKey = 'id_tipo_cuenta_contable';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }
}