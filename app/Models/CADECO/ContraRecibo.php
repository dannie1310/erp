<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:08 AM
 */

namespace App\Models\CADECO;


class ContraRecibo extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 67)
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
    }

    public function disminuyeSaldo(Transaccion $pago){
        $this->saldo = number_format($this->saldo - ($pago->orden_pago->monto * -1),2,".","");
        $this->estado = 1;
        $this->save();
        if($this->saldo<1){
            $this->actualizaEstadoPagada();
        }
    }

    public function actualizaEstadoPagada(){
        $this->estado = 2;
        $this->save();
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_antecedente', 'id_transaccion');
    }
}