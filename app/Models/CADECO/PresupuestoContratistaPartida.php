<?php

namespace App\Models\CADECO;

use Illuminate\Database\Eloquent\Model;

class PresupuestoContratistaPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.presupuestos';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_concepto',
        'precio_unitario',
        'no_cotizado',
        'PorcentajeDescuento',
        'IdMoneda',
        'Observaciones'
    ];

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
        switch($this->IdMoneda)
        {
            case(1):
                return '$ '. number_format($this->precio_unitario, 2, '.', ',');
            break;
            case(2):
                return '$ '. number_format(($this->precio_unitario) / $this->presupuesto->TcUSD, 2, '.', ',');
            break;
            case(3):
                return '$ '. number_format(($this->precio_unitario) / $this->presupuesto->TcEuro, 2, '.', ',');
            break;
        }
    }

    public function getPrecioUnitarioConvertAttribute()
    {
        switch($this->IdMoneda)
        {
            case(1):
                return $this->precio_unitario;
            break;
            case(2):
                return ($this->precio_unitario / $this->presupuesto->TcUSD);
            break;
            case(3):
                return ($this->precio_unitario / $this->presupuesto->TcEuro);
            break;
        }
    }

    public function getPrecioTotalAttribute()
    {
        switch($this->IdMoneda)
        {
            case(1):
                return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario) - ($this->precio_unitario * $this->PorcentajeDescuento), 2, '.', ',') : $this->precio_unitario_format;
            break;
            case(2):
                return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->TcUSD)) - ((($this->precio_unitario) / $this->presupuesto->TcUSD) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0), 2, '.', ',') : $this->precio_unitario_format;
            break;
            case(3):
                return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->TcEuro)) - ((($this->precio_unitario) / $this->presupuesto->TcEuro) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0), 2, '.', ',') : $this->precio_unitario_format;
            break;
        }
    }

    public function getPrecioTotalMonedaAttribute()
    {
        switch($this->IdMoneda)
        {
            case(1):
                return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario) - ($this->precio_unitario * $this->PorcentajeDescuento), 2, '.', ',') : $this->precio_unitario_format;
            break;
            case(2):
                return ($this->concepto) ? '$ '. number_format((($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->TcUSD)) - ((($this->precio_unitario) / $this->presupuesto->TcUSD) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0)) * ($this->presupuesto->TcUSD), 2, '.', ',') : $this->precio_unitario_format;
            break;
            case(3):
                return ($this->concepto) ? '$ '. number_format((($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->TcEuro)) - ((($this->precio_unitario) / $this->presupuesto->TcEuro) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0)) * ($this->presupuesto->TcEuro), 2, '.', ',') : $this->precio_unitario_format;
            break;
        }
    }

    /**
     * Precio total contemplando cantidad, tipo de cambio y descuento
     * @return float|int
     */
    public function getTotalPrecioMonedaAttribute()
    {
        switch ($this->IdMoneda)
        {
            case (1):
                return ($this->concepto ? $this->concepto->cantidad_presupuestada : 1) * $this->precio_compuesto;
                break;
            case (2):
                return($this->presupuesto->TcUSD) ? ($this->concepto ? $this->concepto->cantidad_presupuestada : 1) * $this->precio_compuesto * $this->presupuesto->TcUSD : $this->cantidad * $this->precio_compuesto * $this->tipo_cambio;
                break;
            case (3):
                return ($this->presupuesto->TcEuro) ? ($this->concepto ? $this->concepto->cantidad_presupuestada : 1) * $this->precio_compuesto * $this->presupuesto->TcEuro : ($this->concepto ? $this->concepto->cantidad_presupuestada : 1) * $this->precio_compuesto * $this->tipo_cambio;
                break;
            /*case (4):
                return ($this->concepto ? $this->concepto->cantidad_presupuestada : 1) * $this->precio_compuesto * $this->tipo_cambio;
                break;*/
        }
    }


    /**
     * Precio contemplando descuento y tipo de cambio
     * @return float|int|mixed
     */
    public function getPrecioUnitarioCompuestoAttribute()
    {
        switch ($this->IdMoneda)
        {
            case (1):
                return $this->precio_compuesto;
                break;
            case (2):
                return ($this->presupuesto->TcUSD) ? $this->precio_compuesto * $this->presupuesto->TcUSD : $this->precio_compuesto * $this->tipo_cambio;
                break;
            case (3):
                return ($this->presupuesto->TcEuro) ? $this->precio_compuesto * $this->presupuesto->TcEuro : $this->precio_compuesto * $this->tipo_cambio;
                break;
        }
    }

    /**
     * Precio Compuesto contemplando descuentos al precio unitario.
     * @return float|int|mixed
     */
    public function getPrecioCompuestoAttribute()
    {
        return $this->PorcentajeDescuento != 0 ? $this->precio_unitario - ($this->precio_unitario * $this->PorcentajeDescuento / 100) : $this->precio_unitario;
    }

    /**
     * Precio compuesto descuentos, precio unitario y la cantidad
     * @return float|int
     */
    public function getPrecioCompuestoTotalAttribute()
    {
        return $this->precio_compuesto * ($this->concepto ? $this->concepto->cantidad_presupuestada : 1);
    }

    public function getTipoCambioAttribute()
    {
        return $this->moneda->cambio ? $this->moneda->cambio->cambio : 1;
    }
}
