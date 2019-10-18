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

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function generaOrdenPago($data)
    {
        $datos = [
            'id_antecedente'=>$this->id_antecedente,
            'id_referente'=>$this->id_transaccion,
            'monto'=>-1*abs($data->monto_pagado),
            'fecha'=>$data->fecha_pago,
            'id_empresa'=>$this->id_empresa,
            'id_moneda'=> $data->id_moneda,
        ];
        $ordenPago= OrdenPago::create($datos);
        return $ordenPago->generaPago($data);
    }

    public function ordenesPago(){
        return $this->hasMany(OrdenPago::class, 'id_referente', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_transaccion', 'id_transaccion');
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
}
