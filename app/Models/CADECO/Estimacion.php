<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:12 PM
 */

namespace App\Models\CADECO;


class Estimacion extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 52)
                ->where('estado', '!=', -2);
        });
    }

    public function subcontrato(){
        return $this->hasOne(Subcontrato::class, 'id_transaccion', 'id_antecedente');
    }
}