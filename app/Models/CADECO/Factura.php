<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:09 PM
 */

namespace App\Models\CADECO;


use App\Models\MODULOSSAO\ControlRemesas\Documento;

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

    public function orden_pago()
    {
        return $this->belongsTo(OrdenPago::class, 'id_transaccion','id_referente');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }



    public function generaOrdenPago($data)
    {


        if (is_null($this->orden_pago)){
            $datos = [
                    'id_antecedente'=>$this->id_antecedente,
                    'id_referente'=>$this->id_transaccion,
                    'monto'=>-1*abs($data->monto_pagado),
                    'fecha'=>$data->fecha_pago,
                    'id_empresa'=>$this->id_empresa,
                    'id_moneda'=> $data->id_moneda,
            ];


            $ordenPago= $this->orden_pago()->create($datos);

            $orden_pago = OrdenPago::query()->find($ordenPago->id_transaccion);

             return $orden_pago->generaPago($data);

        }else{
            $orden_pago = OrdenPago::query()->find($this->orden_pago->id_transaccion);

            return $orden_pago->generaPago($data);

        }

    }



    public function ordenPago(){
        return $this->belongsTo(OrdenPago::class, 'id_transaccion', 'id_referente');
    }

    public function partidas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function scopePendientePago($query){
        return $query->whereIn('estado', [1,2]);
    }

    public function scopeConDocumento($query){
        return $query->has('documento');
    }

    public function getAutorizadoAttribute(){
        $pagar = $this->monto * $this->tipo_cambio;
        return '$ ' . number_format($pagar,2);
    }


    public function getEstado($estado)
    {
        if($estado==0){
            $estado='Registrada';
        }
        if ($estado==1){
            $estado='Revisada';
        }
        if($estado==2){
            $estado='Pagada';
        }
        return $estado;
    }

    public function getTipo($tipo)
    {

        if($tipo==0){
            $tipo='Factura';
        }
        if($tipo==1){
            $tipo='Gastos Varios';
        }
        if($tipo==65537){
            $tipo='Materiales / Servicios';
        }

        return $tipo;
    }
}
