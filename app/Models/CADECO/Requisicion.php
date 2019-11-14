<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:46 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Compras\RequisicionComplemento;

class Requisicion extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 16);
        });
    }

    public function partidas()
    {
        return $this->hasMany(RequisicionPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(RequisicionComplemento::class,'id_transaccion', 'id_transaccion');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }
}