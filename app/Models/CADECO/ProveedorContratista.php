<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:24 PM
 */

namespace App\Models\CADECO;



class ProveedorContratista extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('tipo_empresa', [1,2,3]);
        });
    }
}