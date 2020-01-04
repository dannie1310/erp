<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/01/2020
 * Time: 12:49 PM
 */

namespace App\Models\CADECO;


class Cliente extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_empresa', '=', 16);
        });
    }

    public function getTipoAttribute()
    {
        if($this->tipo_cliente == 1){
            return 'Comprador';
        }
        if($this->tipo_cliente == 2){
            return 'Inversionista';
        }
        if($this->tipo_cliente == 3){
            return 'Comprador / Inversionista';
        }
    }
}