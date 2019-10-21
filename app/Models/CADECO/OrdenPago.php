<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;


class OrdenPago extends Transaccion
{
    public const TIPO_ANTECEDENTE = 67;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'fecha',
        'id_obra',
        'monto',
        'referencia',
        'tipo_transaccion',
        "id_empresa",
        "id_moneda",
        "id_usuario"
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 68)
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_referente','id_transaccion');
    }

    public function generaPago($data)
    {

        $datos = [
            'numero_folio' => $this->numero_folio,
            'refencia'=>$data->referencia_pago,
            'estado' =>2,
            'id_cuenta'=>$data->id_cuenta_cargo,
            'fecha'=>$data->fecha_pago,
            'monto'=>-1*abs($data->monto_pagado),
            'id_empresa'=>$this->id_empresa,
            'destino'=>$this->empresa->razon_social,
            'id_moneda'=>$data->id_moneda,
            'observaciones'=>$this->factura->observaciones,
            'cumpliemnto'=>$data->fecha_pago,
            'vencimiento'=>$data->fecha_pago,
        ];

        $pago = Pago::query()->create($datos);

        return $pago->id_transaccion;
    }
}
