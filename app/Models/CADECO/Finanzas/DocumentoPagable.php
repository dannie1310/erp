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
class DocumentoPagable extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();
    }

    public function partida_layout()
    {
        return $this->HasOne(LayoutPagoPartida::class,'id_transaccion','id_transaccion');
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
            return $this->monto;
        }elseif($this->tipo_transaccion == 65){
            return $this->saldo;
        }
    }

    public function getACuentaFormatAttribute()    {
        return '$ '.number_format(abs($this->ordenesPago->sum('monto')),2,".",",");
    }

    public function getSaldoFormatAttribute()    {
        return '$ '.number_format(abs($this->saldo),2,".",",");
    }



}