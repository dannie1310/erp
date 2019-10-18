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
        return $this->belongsTo(Transaccion::class, 'id_antecedente', 'id_transaccion')
            ->where('tipo_transaccion', '=', 82);
    }

    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function pago_cuenta()
    {
        return $this->belongsTo(PagoACuenta::class, 'id_referente', 'id_referente');
    }


    public function generaPago($data)
    {
      if(is_null($this->pago_cuenta)){
          $data = array(
              "id_empresa" => $this->id_empresa,
              "id_moneda" => $this->id_moneda,
              "fecha" => $this->fecha,
              "cumplimiento" => $this->fecha,
              "vencimiento" => $this->fecha,
              "monto" => -1 * abs($this->monto),
              "referencia" => $this->referencia,
              "id_cuenta" => $this->id_cuenta,
              "destino" => $this->destino,
              "observaciones" => $this->observaciones,
              "id_referente"=> $this->id_referente,
          );

          $pago = PagoACuenta::query()->create($data);
          return $pago->id_transaccion;
      }else{

          return $this->pago_cuenta->id_transaccion;
      }


    }

}
