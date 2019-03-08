<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:55 PM
 */

namespace App\Models\CADECO;


class Subcontrato extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 51)
                ->where('estado', '!=', -2);
        });
    }

    public function contratoProyectado(){
        return $this->hasOne(ContratoProyectado::class, 'id_transaccion', 'id_antecedente');
    }

    public function estimaciones (){
        return $this->hasMany(Estimacion::class, 'id_antecedente', 'id_transaccion');
    }

    public function empresa(){
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }
}