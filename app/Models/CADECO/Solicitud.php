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

    public function generaPago($data)
    {

        $data = array(
            "id_empresa" => $this->id_empresa,
            "id_moneda" => $this->id_moneda,
            "fecha" => $this->fecha,
            "cumplimiento" => $this->fecha,
            "vencimiento" => $this->fecha,
            "monto" => -1 * abs($this->monto),
            "referencia" => $this->referencia,
        );

                $data["id_cuenta"] = $this->id_cuenta;
                $data["destino"] = $this->destino;
                $data["observaciones"] = $this->observaciones;
                $data['id_referente']= $this->id_referente;
                $pago = PagoACuenta::query()->create($data);
                return $pago->id_transaccion;

    }

}
