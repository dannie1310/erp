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

    public function documento(){
        return $this->belongsTo(Documento::class, 'id_transaccion', 'IDTransaccionCDC');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
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
}
