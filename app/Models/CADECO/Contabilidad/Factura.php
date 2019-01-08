<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:18 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\CADECO\Transaccion;

class Factura extends Transaccion
{
    protected static function boot() {
        parent::boot();
        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 65);
        });
    }
}