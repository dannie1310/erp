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
    public const TIPO = 68;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'fecha',
        'id_obra',
        'monto',
        'referencia',
        'tipo_transaccion',
        'tipo_cambio',
        "id_empresa",
        "id_moneda",
        "id_usuario",
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

    public function pago()
    {
        return $this->belongsTo(PagoFactura::class, 'numero_folio', 'numero_folio');
    }

    public function partidas(){
        return $this->hasMany(ItemOrdenPago::class, 'id_transaccion', 'id_transaccion');
    }

    public static function calcularFolio()
    {
        $pago = Pago::orderBy('numero_folio', 'DESC')->first();
        return $pago ? $pago->numero_folio + 1 : 1;
    }

    public function generaPago($data)
    {
            if (is_null($this->pago)){
                $datos = [
                    'numero_folio' => $this->numero_folio,
                    'referencia'=>$data["referencia_pago"],
                    'id_cuenta'=>$data["id_cuenta_cargo"],
                    'fecha'=>$data["fecha_pago"],
                    'monto'=>-1*abs($data["monto_pagado"]),
                    'id_empresa'=>$this->id_empresa,
                    'destino'=>mb_substr($this->empresa->razon_social,0,124),
                    'id_moneda'=>$data["id_moneda_cuenta_cargo"],
                    'observaciones'=>$this->factura->observaciones,
                    'cumplimiento'=>$data["fecha_pago"],
                    'vencimiento'=>$data["fecha_pago"],
                ];
                $pago = PagoFactura::create($datos);
                return $pago;
            }else{

                return $this->pago;
            }
    }

    /**
     * Este método implementa la lógica actualización de control de obra del procedimiento almacenado sp_aplicar_pagos
     * y se detona al registrar una orden de pago
     */
    public function actualizaControlObra()
    {
        if($this->factura){
            $items_factura = $this->factura->items;
            foreach($items_factura as $item_factura)
            {
                $item_factura->actualizaControlObra($this);
            }
        } else {
            abort(500, "No se encontraron las partidas de la factura");
        }

    }

    public function regresarSaldo()
    {
        $saldo = $this->factura->saldo + ((-1) * $this->monto);
        $this->factura->saldo = $saldo;
        $this->factura->contra_recibo->saldo = $saldo;
        if($this->factura->estado == 2)
        {
            $this->factura->estado = 1;
            $this->factura->contra_recibo->estado = 1;
        }
        $this->factura->save();
        $this->factura->contra_recibo->save();
    }
}
