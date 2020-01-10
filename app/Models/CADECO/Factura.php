<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:09 PM
 */

namespace App\Models\CADECO;


use App\Models\MODULOSSAO\ControlRemesas\Documento;
use Illuminate\Support\Facades\DB;
class Factura extends Transaccion
{
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 65)
                ->where('estado', '!=', -2);
        });
    }

    public function contra_recibo()
    {
        return $this->belongsTo(ContraRecibo::class, 'id_antecedente', 'id_transaccion');
    }

    public function documento(){
        return $this->belongsTo(Documento::class, 'id_transaccion', 'IDTransaccionCDC');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function ordenesPago(){
        return $this->hasMany(OrdenPago::class, 'id_referente', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function items()
    {
        return $this->hasMany(ItemFactura::class, 'id_transaccion', 'id_transaccion');
    }

    public function pagos(){
        return $this->hasManyThrough(PagoFactura::class,OrdenPago::class, 'id_referente','numero_folio','id_transaccion','id_transaccion');
    }

    public function generaOrdenPago($data)
    {
        try{
            // TODO: Obtener el monto de los pagos relacionados a la factura para determinar si se debe actualizar el estado
            DB::connection('cadeco')->beginTransaction();
            $cuenta_cargo = Cuenta::find($data["id_cuenta_cargo"]);
            $saldo_esperado = $this->saldo - ($data["monto_pagado_transaccion"]);
            $saldo_esperado_cuenta = $cuenta_cargo->saldo_real - ($data["monto_pagado"]);

            $datos = [
                'id_antecedente'=>$this->id_antecedente,
                'id_referente'=>$this->id_transaccion,
                'monto'=>-1*abs($data["monto_pagado_transaccion"]),
                'tipo_cambio'=>$data["tipo_cambio"],
                'fecha'=>$data["fecha_pago"],
                'id_empresa'=>$this->id_empresa,
                'id_moneda'=> $this->id_moneda,
            ];
            $ordenPago= OrdenPago::create($datos);
            $pago = $ordenPago->generaPago($data);

            $this->validaSaldos($saldo_esperado, $saldo_esperado_cuenta, $pago);
            DB::connection('cadeco')->commit();
            return $pago;
        }
        catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }

    }

    public function scopePendientePago($query){
        return $query->where('estado', '=', 1)
            ->where('saldo', '>', 0.99);
    }

    public function scopeConDocumento($query){
        return $query->has('documento');
    }

    public function getAutorizadoAttribute(){
        $pagar = $this->monto * $this->tipo_cambio;
        return '$ ' . number_format($pagar,2);
    }

    public function getEstadoStringAttribute()
    {
        $estado = "";
        if($this->estado==0){
            $estado='Registrada';
        }
        elseif ($this->estado==1 && abs($this->ordenesPago->sum('monto'))<1){
            $estado='Revisada';
        }elseif ($this->estado==1 && abs($this->monto+$this->ordenesPago->sum('monto'))>1){
            $estado='Saldo Pendiente ';
        }
        elseif($this->estado==2){
            $estado='Pagada';
        }
        return $estado;
    }

    public function getACuentaFormatAttribute()    {
        return '$ '.number_format(abs($this->ordenesPago->sum('monto')),2,".",",");
    }

    public function getSaldoFormatAttribute()    {
        return '$ '.number_format(abs($this->saldo),2,".",",");
    }

    public function getTipoTransaccionStringAttribute()   {
        if($this->opciones==0){
            $tipo='Factura';
        }
        if($this->opciones==1){
            $tipo='Gastos Varios';
        }
        if($this->opciones==65537){
            $tipo='Materiales / Servicios';
        }
        return $tipo;
    }

    private function validaSaldos($saldo_esperado, $saldo_esperado_cuenta, $pago){
        $this->refresh();
        $pago->load("cuenta");
        if(abs($saldo_esperado_cuenta-$pago->cuenta->saldo_real)>1){
            abort(400, 'Hubo un error durante la actualización del saldo de la cuenta por el pago de la factura.');
        }
        if(abs($saldo_esperado-$this->saldo)>1){
            abort(400, 'Hubo un error durante la actualización del saldo de la factura');
        }
    }

    public function disminuyeSaldo(Transaccion $pago){
        $this->saldo = number_format($this->saldo - ($pago->orden_pago->monto * -1),2,".","");
        $this->save();
        if($this->saldo<1){
            $this->actualizaEstadoPagada();
        }
    }

    public function actualizaEstadoPagada(){
        $this->estado = 2;
        $this->save();
    }

    public function getFactorIvaAttribute()
    {
        if(($this->monto-$this->impuesto)>0) {
            return $this->monto / ($this->monto-$this->impuesto);
        } else {
            return 1;
        }
    }
}
