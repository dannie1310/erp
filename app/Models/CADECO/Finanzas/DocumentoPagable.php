<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO\Finanzas;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Moneda;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
class DocumentoPagable extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->whereIn('tipo_transaccion',[65,72])
                ->where('estado', '!=', -2);
        });
    }

    public function partida_layout()
    {
        return $this->HasOne(LayoutPagoPartida::class,'id_transaccion','id_transaccion');
    }

    public function getMontoAutorizadoRemesaAttribute(){
        if($this->documento){
            return $this->documento->importe_total_procesado;
        }else{
            return 0;
        }
    }

    public function getIdDocumentoRemesaAttribute(){
        if($this->documento){
            return $this->documento->IDDocumento;
        }else{
            return null;
        }
    }

    public function getBeneficiarioAttribute(){
        if($this->empresa){
            return (string)$this->empresa->razon_social;
        }else{
            return (string) $this->destino;
        }
    }

    public function getReferenciaPagableAttribute(){
        if($this->tipo_transaccion == 72){
            return (string)'S/P '.$this->numero_folio_format;
        }elseif($this->tipo_transaccion == 65){
            return (string)$this->referencia;
        }

    }

    public function getSaldoPagableAttribute(){
        if($this->tipo_transaccion == 72){
            if($this->estado ==2){
                return 0;
            }elseif($this->estado == 1){
                return number_format($this->monto,2,".","");
            }

        }elseif($this->tipo_transaccion == 65){
            return number_format($this->saldo,2,".","");
        }
    }

    public function getACuentaFormatAttribute()    {
        return '$ '.number_format(abs($this->ordenesPago->sum('monto')),2,".",",");
    }

    public function getSaldoFormatAttribute()    {
        return '$ '.number_format(abs($this->saldo),2,".",",");
    }

    public function getSaldoPagableFormatAttribute()    {
        return '$ '.number_format(abs($this->saldo_pagable),2,".",",");
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function documento(){
        return $this->belongsTo(Documento::class, 'id_transaccion', 'IDTransaccionCDC');
    }
}