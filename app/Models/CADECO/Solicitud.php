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

    public function pago()
    {
        return $this->HasOne(Pago::class,'id_antecedente','id_transaccion');
    }

    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function pago_cuenta()
    {
        return $this->belongsTo(PagoACuenta::class, 'id_referente', 'id_referente');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function generaPago($data)
    {
        $pago = null;
        if($this->opciones == 1){
            $pago = SolicitudReposicionFF::find($this->id_transaccion)->generaPago($data);
        }
        return $pago->id_transaccion;
    }

    public function scopePendientePago($query)
    {
        return $query->where('estado', '!=', 2);
    }
}
