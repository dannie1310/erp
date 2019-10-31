<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:47 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Finanzas\TransaccionRubro;
use Illuminate\Support\Facades\DB;

class SolicitudAnticipoDestajo extends Solicitud
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'tipo_transaccion',
        'id_obra',
        'estado',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'destino',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'opciones',
        'fecha',
        'id_costo',
        'id_usuario',
        'tipo_cambio'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 131073);
        });
    }

    public function orden_compra(){
        return $this->hasOne(OrdenCompra::class, 'id_transaccion', 'id_referente');
    }

    public function subcontrato(){
        return $this->hasOne(Subcontrato::class,'id_transaccion', 'id_referente');
    }

    public function pago(){
        return $this->HasOne(PagoAnticipoDestajo::class,'id_antecedente','id_transaccion');
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
            $datos_pago = array(
                "id_antecedente" => $this->id_transaccion,
                "id_referente" => $this->id_referente,
                "fecha" => $data["fecha_pago"],
                "estado" => 1,
                "id_cuenta" =>  $data["id_cuenta_cargo"],
                "id_empresa" =>  $this->id_empresa,
                "destino" =>  $this->destino,
                "id_moneda" =>  $data["id_moneda_cuenta_cargo"],
                "tipo_cambio"=>1/$data["tipo_cambio"],
                "cumplimiento" => $data["fecha_pago"],
                "vencimiento" => $data["fecha_pago"],
                "monto" => -1 * abs($data["monto_pagado"]),
                "saldo" => -1 * abs($data["monto_pagado"]),
                "referencia" => $data["referencia_pago"],
                "observaciones" => $this->observaciones,
            );
            $pago = $this->pago()->create($datos_pago);
            $this->validaPago($pago);
            $this->validaSaldos($saldo_esperado_cuenta, $pago);
            DB::connection('cadeco')->commit();
            return $pago;
        }
    }

    private function validaPago(PagoAnticipoDestajo $pago){
        if(!$pago){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante el registro del pago de anticipo');
        }
    }

    private function validaSaldos($saldo_esperado_cuenta, $pago){
        $pago->load("cuenta");
        if(abs($saldo_esperado_cuenta-$pago->cuenta->saldo_real)>1){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante la actualización del saldo de la cuenta por el pago de la solicitud de anticipo / destajo.');
        }
    }
    public function generaSolicitudComplemento()
    {
        //TODO: MEJORAR FORMA DE OBTNER EL TIPO DE CAMBIO REQUERIDO
        DB::connection('cadeco')->beginTransaction();
        $datos_solicitud = array(
            "id_referente" => $this->id_referente,
            "fecha" => $this->fecha,
            "id_empresa" =>  $this->id_empresa,
            "destino" =>  $this->destino,
            "id_moneda" =>  $this->id_moneda,
            "cumplimiento" => $this->cumplimiento,
            "vencimiento" => $this->vencimiento,
            "monto" => number_format($this->monto-(abs($this->pago->monto *  (1/$this->pago->tipo_cambio))),2,".",""),
            "saldo" => number_format($this->monto-(abs($this->pago->monto *  (1/$this->pago->tipo_cambio))),2,".",""),
            "destino" => $this->destino,
            "observaciones" => $this->observaciones,
        );
        $solicitud = SolicitudAnticipoDestajo::create($datos_solicitud);
        #$this->load("pago");
        $this->monto = number_format(abs($this->pago->monto * (1/$this->pago->tipo_cambio)),2,".","");
        $this->saldo = number_format(abs($this->pago->monto * (1/$this->pago->tipo_cambio)),2,".","");
        $this->save();
        DB::connection('cadeco')->commit();
    }
}