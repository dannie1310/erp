<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 08:03 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaContable extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_cuentas_contables';
    protected $primaryKey = 'id_int_cuenta_contable';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function tipo() {
        return $this->belongsTo(TipoCuentaContable::class, 'id_int_tipo_cuenta_contable');
    }
}