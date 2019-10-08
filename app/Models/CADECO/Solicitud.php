<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


class Solicitud extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72);
        });
    }

    public function pago(){
        return $this->belongsTo(Transaccion::class, 'id_antecedente', 'id_transaccion')
            ->where('tipo_transaccion', '=', 82);
    }

    public function fondo(){
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }
}
