<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:04 AM
 */

namespace App\Models\CADECO;


use App\Facades\Context;

class Banco extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra())
                         ->where('tipo_empresa', '=', 8);
        });
    }
}