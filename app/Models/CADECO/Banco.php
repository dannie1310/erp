<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/02/2019
 * Time: 06:26 PM
 */

namespace App\Models\CADECO;


class Banco extends Empresa
{

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query){
           return $query->where('tipo_empresa', '=', 8);
        });
    }

    public function cuentaBancaria(){
        return $this->hasMany(Cuenta::class, 'id_empresa', 'id_empresa');
    }
}