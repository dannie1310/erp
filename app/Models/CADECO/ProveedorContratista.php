<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:24 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Suministrados;



class ProveedorContratista extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('tipo_empresa', [1,2,3]);
        });
    }

    public function sucursales(){
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }

    public function suministrados(){
        return $this->hasMany(Suministrados::class, 'id_empresa', 'id_empresa');
    }
}