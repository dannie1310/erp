<?php

namespace App\Models\CADECO;

use Illuminate\Database\Eloquent\Model;

class PresupuestoContratistaPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.presupuestos';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;


    public function concepto()
    {
        return $this->belongsTo(Contrato::class, 'id_concepto');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'IdMoneda', 'id_moneda');
    }

    public function presupuesto()
    {
        return $this->hasOne(PresupuestoContratista::class, 'id_transaccion');
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ '. number_format($this->precio_unitario, 2, '.', ',');
    }

    public function getPrecioTotalAttribute()
    {
        return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario), 2, '.', ',') : $this->precio_unitario_format; 
    }

    public function getPrecioTotalMonedaAttribute()
    {
        switch ($this->IdMoneda)
        {
            case(1):
                return ($this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario), 2, '.', ',') : '----------';
            break;
            case(2):
                return ($this->presupuesto && $this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario * $this->presupuesto->TcUSD), 2, '.', ',') : '--------';
            break;
            case(3):
                return ($this->presupuesto && $this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario * $this->presupuesto->TcEuro), 2, '.', ',') : '----------';
            break;
        }
    }
}