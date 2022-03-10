<?php


namespace App\Models\CADECO;


use Illuminate\Support\Facades\DB;

class SolicitudListaRaya extends Solicitud
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        "id_obra",
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
                ->where("opciones","=",65537);
        });
    }

    public function listaRaya()
    {
        return $this->belongsTo(ListaRaya::class, 'id_referente', 'id_transaccion');
    }

    public function prestacion()
    {
        return $this->belongsTo(Prestacion::class, 'id_referente','id_transaccion');
    }

    public function pago()
    {
        return $this->HasOne(PagoListaRaya::class,'id_antecedente','id_transaccion');
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
            $datos_pago = array(
                "id_antecedente" => $this->id_transaccion,
                "id_referente" => $this->id_referente,
                "fecha" => $data["fecha_pago"],
                "estado" => 1,
                "id_cuenta" =>  $data["id_cuenta_cargo"],
                "id_costo" =>  $this->id_costo,
                "id_empresa" =>  $this->id_empresa,
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
            $this->validaSaldos($saldo_esperado_cuenta, $pago);
            DB::connection('cadeco')->commit();
            return $pago;
        }
    }

    private function validaPago(Pago $pago){
        if(!$pago){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante el registro del pago');
        }
    }

    private function validaSaldos($saldo_esperado_cuenta, $pago){
        $pago->load("cuenta");
        if(abs($saldo_esperado_cuenta - $pago->cuenta->saldo_real) > 1){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante la actualización del saldo de la cuenta por el pago de la solicitud de lista de raya.');
        }
    }

    /**
     * Lógica para registrar un pago de lista de raya, cálculos en el inventario, saldo y cambios de estado
     * @param $pago
     */
    public function cambiosAlPagar($pago)
    {
        if($this->listaRaya)
        {
            foreach ($this->listaRaya->items as $item) {
                $monto_a_modificar = ROUND($item->inventario->monto_pagado - $item->importe * (-$this->monto / $this->listaRaya->monto), 2);
                $item->inventario->monto_pagado = $monto_a_modificar;
                $item->inventario->save();
            }
            $this->listaRaya->saldo = ROUND(($this->listaRaya->saldo - (-1 * $pago->saldo)), 2);
            if($this->listaRaya->saldo == 0.0)
            {
                $this->listaRaya->estado = 2;
            }
            $this->listaRaya->save();
        }

        if($this->prestacion)
        {
            foreach ($this->prestacion->items as $item) {
                $monto_a_modificar = ROUND($item->inventario->monto_pagado - $item->importe * (-$this->monto / $this->prestacion->monto), 2);
                $item->inventario->monto_pagado = $monto_a_modificar;
                $item->inventario->save();
            }
            $this->prestacion->saldo = ROUND(($this->prestacion->saldo - (-1 * $pago->saldo)), 2);
            if($this->prestacion->saldo == 0.0)
            {
                $this->prestacion->estado = 2;
            }
            $this->prestacion->save();
        }

        if(abs($pago->monto) == abs($this->monto))
        {
            $this->actualizaEstadoPagada();
        }
    }
}
