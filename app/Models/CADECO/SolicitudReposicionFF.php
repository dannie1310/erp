<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;
use Illuminate\Support\Facades\DB;
class SolicitudReposicionFF extends Solicitud
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72)
                ->where("opciones","=",1);
        });
    }

    public function pago()
    {
        return $this->HasOne(PagoReposicionFF::class,'id_antecedente','id_transaccion');
    }

    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function generaPago($data)
    {
        $pago = $this->pago;
        if($pago){
            $this->actualizaEstadoPagada();
            return $pago;
        }else{
            DB::connection('cadeco')->beginTransaction();
            $saldo_esperado_cuenta = $data->cuenta->saldo_real - ($data["monto_pagado"]);
            $saldo_esperado_fondo = $this->fondo->saldo + $data["monto_pagado"];
            $datos_pago = array(
                "id_antecedente" => $this->id_transaccion,
                "id_referente" => $this->id_referente,
                "fecha" => $data["fecha_pago"],
                "estado" => 1,
                "id_cuenta" =>  $data["id_cuenta_cargo"],
                "id_moneda" =>  $data["id_moneda"],
                "cumplimiento" => $data["fecha_pago"],
                "vencimiento" => $data["fecha_pago"],
                "monto" => -1 * abs($data["monto_pagado"]),
                "saldo" => -1 * abs($data["monto_pagado"]),
                "referencia" => $data["referencia_pago"],
                "destino" => $this->destino,
                "observaciones" => $this->observaciones,
            );
            $pago = $this->pago()->create($datos_pago);
            $this->validaPago($pago);
            $this->validaSaldos($saldo_esperado_cuenta, $saldo_esperado_fondo, $pago);
            DB::connection('cadeco')->commit();
            return $pago;
        }
    }

    private function validaPago(PagoReposicionFF $pago){
        if(!$pago){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante el registro del pago');
        }
    }

    private function validaSaldos($saldo_esperado_cuenta, $saldo_esperado_fondo, $pago){
        $this->load("fondo");
        $pago->load("cuenta");
        if(abs($saldo_esperado_cuenta-$pago->cuenta->saldo_real)>1){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante la actualización del saldo de la cuenta por el pago de la solicitud de reposición de fondo.');
        }
        if(abs($saldo_esperado_fondo-$this->fondo->saldo)>1){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante la actualización del saldo del fondo');
        }
    }
}