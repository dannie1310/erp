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

    protected $fillable = [
        "id_referente" ,
        "fecha" ,
        "id_moneda",
        "cumplimiento",
        "vencimiento",
        "monto",
        "saldo",
        "destino",
        "observaciones",
    ];

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
            $cuenta_cargo = Cuenta::find($data["id_cuenta_cargo"]);
            $saldo_esperado_cuenta = $cuenta_cargo->saldo_real - ($data["monto_pagado"]);
            $saldo_esperado_fondo = $this->fondo->saldo + ($data["monto_pagado"] * ($data["tipo_cambio"]));
            $datos_pago = array(
                "id_antecedente" => $this->id_transaccion,
                "id_referente" => $this->id_referente,
                "fecha" => $data["fecha_pago"],
                "estado" => 1,
                "id_cuenta" =>  $data["id_cuenta_cargo"],
                "destino" =>  $this->destino,
                "id_moneda" =>  $data["id_moneda_cuenta_cargo"],
                "tipo_cambio"=>$data["tipo_cambio"],
                "cumplimiento" => $data["fecha_pago"],
                "vencimiento" => $data["fecha_pago"],
                "monto" => -1 * abs($data["monto_pagado"]),
                "saldo" => -1 * abs($data["monto_pagado"]),
                "referencia" => $data["referencia_pago"],
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
            abort(400, 'Hubo un error durante la actualización del saldo del fondo '.$this->fondo->descripcion);
        }
    }

    public function generaSolicitudComplemento()
    {
        //TODO: MEJORAR FORMA DE OBTNER EL TIPO DE CAMBIO REQUERIDO
        DB::connection('cadeco')->beginTransaction();
        $datos_solicitud = array(
            "id_referente" => $this->id_referente,
            "fecha" => $this->fecha,
            "id_moneda" =>  $this->id_moneda,
            "cumplimiento" => $this->cumplimiento,
            "vencimiento" => $this->vencimiento,
            "monto" => number_format($this->monto-(abs($this->pago->monto *  (1/$this->pago->tipo_cambio))),2,".",""),
            "saldo" => number_format($this->monto-(abs($this->pago->monto *  (1/$this->pago->tipo_cambio))),2,".",""),
            "destino" => $this->destino,
            "observaciones" => $this->observaciones,
        );
        $solicitud = SolicitudReposicionFF::create($datos_solicitud);
        #$this->load("pago");
        $this->monto = number_format(abs($this->pago->monto * (1/$this->pago->tipo_cambio)),2,".","");
        $this->saldo = number_format(abs($this->pago->monto * (1/$this->pago->tipo_cambio)),2,".","");
        $this->save();
        DB::connection('cadeco')->commit();
    }
}