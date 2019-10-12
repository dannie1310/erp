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


        switch ($this->tipo_transaccion) {
            case 65:
                $data["id_antecedente"] = $this->id_antecedente;
                $data["id_referente"] = $this->id_transaccion;
                unset($data["referencia"]);
                $o_pago = OrdenPago::query()->create($data);
                $o_pago = OrdenPago::query()->where('id_transaccion', '=', $o_pago->id_transaccion)->first();
                unset($data["id_antecedente"]);
                unset($data["id_referente"]);
                $data["numero_folio"] = $o_pago->numero_folio;
                $data["referencia"] = $this->referencia;
                $data["estado"] = 2;
                $data["id_cuenta"] = $this->id_cuenta;
                $data["destino"] = $this->destino;
                $data["observaciones"] = $this->observaciones;
                $pago = Pago::query()->create($data);
                return $pago->id_transaccion;
                break;

            case 72:
                $data["id_cuenta"] = $this->id_cuenta;
                $data["destino"] = $this->destino;
                $data["observaciones"] = $this->observaciones;
                $pago = PagoACuenta::query()->create($data);
                return $pago->id_transaccion;
                break;

            default:
                $data["id_cuenta"] = $this->id_cuenta;
                $data["destino"] = $this->destino;
                $data["observaciones"] = $this->observaciones;
                $pago = PagoACuenta::query()->create($data);
                return $pago->id_transaccion;
                break;


        }


    }

}
