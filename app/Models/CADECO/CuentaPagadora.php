<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:00 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaBanco;
use App\Models\CADECO\Finanzas\CtgTipoCuentaObra;
use Illuminate\Database\Eloquent\Model;

class CuentaPagadora extends Cuenta
{

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_tipo_cuentas_obra', '=', 1);
        });
    }

}
