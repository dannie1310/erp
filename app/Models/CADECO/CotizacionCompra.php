<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\CotizacionComplemento;

class CotizacionCompra  extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 18);
        });
    }

    public function cotizaciones() {
        return $this->hasMany(Cotizacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion');
    }

}