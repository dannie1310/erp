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
        switch ($this->IdMoneda) {
            case(1):
                return '$ ' . number_format($this->precio_unitario, 2, '.', ',');
                break;
            case(2):
                return '$ ' . number_format(($this->precio_unitario) / $this->presupuesto->dolar, 2, '.', ',');
                break;
            case(3):
                return '$ ' . number_format(($this->precio_unitario) / $this->presupuesto->euro, 2, '.', ',');
                break;
            case(4):
                return '$ ' . number_format(($this->precio_unitario) / $this->presupuesto->libra, 2, '.', ',');
                break;
        }
    }

    public function getPrecioUnitarioConvertAttribute()
    {
        switch ($this->IdMoneda) {
            case(1):
                return $this->precio_unitario;
                break;
            case(2):
                return ($this->precio_unitario / $this->presupuesto->dolar);
                break;
            case(3):
                return ($this->precio_unitario / $this->presupuesto->euro);
                break;
            case(4):
                return ($this->precio_unitario / $this->presupuesto->libra);
                break;
        }
    }

    public function getPrecioUnitarioDescuentoMonedaOriginalAttribute()
    {
        switch ($this->IdMoneda) {
            case(1):
                return $this->precio_unitario;
                break;
            case(2):
                return ($this->precio_unitario / $this->presupuesto->dolar);
                break;
            case(3):
                return ($this->precio_unitario / $this->presupuesto->euro);
                break;
            case(4):
                return ($this->precio_unitario / $this->presupuesto->libra);
                break;
        }
    }

    public function getPrecioTotalAttribute()
    {
        switch ($this->IdMoneda) {
            case(1):
                return ($this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario) - ($this->precio_unitario * $this->PorcentajeDescuento), 2, '.', ',') : $this->precio_unitario_format;
                break;
            case(2):
                return ($this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->dolar)) - ((($this->precio_unitario) / $this->presupuesto->dolar) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0), 2, '.', ',') : $this->precio_unitario_format;
                break;
            case(3):
                return ($this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->euro)) - ((($this->precio_unitario) / $this->presupuesto->euro) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0), 2, '.', ',') : $this->precio_unitario_format;
                break;
            case(4):
                return ($this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->libra)) - ((($this->precio_unitario) / $this->presupuesto->libra) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0), 2, '.', ',') : $this->precio_unitario_format;
                break;
        }
    }

    public function getPrecioTotalMonedaAttribute()
    {
        switch ($this->IdMoneda) {
            case(1):
                return ($this->concepto) ? '$ ' . number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario) - ($this->precio_unitario * $this->PorcentajeDescuento), 2, '.', ',') : $this->precio_unitario_format;
                break;
            case(2):
                return ($this->concepto) ? '$ ' . number_format((($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->dolar)) - ((($this->precio_unitario) / $this->presupuesto->dolar) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0)) * ($this->presupuesto->dolar), 2, '.', ',') : $this->precio_unitario_format;
                break;
            case(3):
                return ($this->concepto) ? '$ ' . number_format((($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->euro)) - ((($this->precio_unitario) / $this->presupuesto->euro) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0)) * ($this->presupuesto->euro), 2, '.', ',') : $this->precio_unitario_format;
                break;
            case(4):
                return ($this->concepto) ? '$ ' . number_format((($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->libra)) - ((($this->precio_unitario) / $this->presupuesto->libra) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0)) * ($this->presupuesto->libra), 2, '.', ',') : $this->precio_unitario_format;
                break;
        }
    }

    /**
     * Precio contemplando descuento y tipo de cambio
     * @return float|int|mixed
     */
    public function getPrecioUnitarioCompuestoAttribute()
    {
        return $this->precio_unitario;
    }

    /**
     * Precio contemplando descuentos al precio unitario en moneda original.
     * @return float|int|mixed
     */
    public function getPrecioCompuestoAttribute()
    {
        switch ($this->IdMoneda) {
            case (1):
                return $this->precio_unitario;
                break;
            case (2):
                return $this->precio_unitario / $this->presupuesto->dolar;
                break;
            case (3):
                return $this->precio_unitario / $this->presupuesto->euro;
                break;
            case (4):
                return $this->precio_unitario / $this->presupuesto->libra;
                break;
        }
        //return $this->PorcentajeDescuento != 0 ? $this->precio_unitario_simple - ($this->precio_unitario_simple * $this->PorcentajeDescuento / 100) : $this->precio_unitario_simple;
    }

    /**
     * Precio compuesto descuentos, precio unitario y la cantidad en moneda de conversión (PESOS)
     * @return float|int
     */
    public function getPrecioCompuestoTotalAttribute()
    {
        return $this->precio_unitario_compuesto * ($this->concepto ? $this->concepto->cantidad_presupuestada : 1);
    }

    /**
     * Precio  sin descuentos, precio unitario y la cantidad
     * @return float|int
     */
    public function getPrecioSinDescuentoAttribute()
    {
        return $this->precio_unitario * ($this->concepto ? $this->concepto->cantidad_presupuestada : 1);
    }

    /**
     * Precio unitario simple (moneda original con descuento de partida aplicado)
     * @return float|int|mixed
     */
    public function getPrecioUnitarioSimpleAttribute()
    {
        switch ($this->IdMoneda) {
            case (1):
                return $this->precio_unitario;
                break;
            case (2):
                return $this->precio_unitario / $this->presupuesto->dolar;
                break;
            case (3):
                return $this->precio_unitario / $this->presupuesto->euro;
                break;
            case (4):
                return $this->precio_unitario / $this->presupuesto->libra;
                break;
        }
    }

    /**
     * Importe moneda original con descuento de partida aplicado
     * @return float|int|mixed
     */
    public function getImporteSimpleAttribute()
    {
        return $this->precio_unitario_simple * $this->concepto->cantidad_presupuestada;
    }

    /**
     * Importe moneda original con descuento de partida aplicado
     * @return float|int|mixed
     */
    public function getImporteCompuestoAttribute()
    {
        return $this->precio_unitario_compuesto * $this->concepto->cantidad_presupuestada;
    }

    public function getPrecioUnitarioMonedaOriginalAttribute()
    {
        switch ($this->IdMoneda) {
            case (1):
                return $this->precio_unitario;
                break;
            case (2):
                return $this->precio_unitario / $this->presupuesto->dolar;
                break;
            case (3):
                return $this->precio_unitario / $this->presupuesto->euro;
                break;
            case (4):
                return $this->precio_unitario / $this->presupuesto->libra;
                break;
        }
    }

    public function getPorcentajeDescuentoFormatAttribute()
    {
        return number_format($this->PorcentajeDescuento, "2",".","")." %";
    }

    public function getPrecioUnitarioAntesDescuentoAttribute()
    {
        return 100 * $this->precio_unitario_moneda_original /(100-$this->PorcentajeDescuento);
    }

    public function getPrecioUnitarioAntesDescuentoFormatAttribute()
    {
        return "$".number_format($this->precio_unitario_antes_descuento, 2, '.', ',');
    }

    public function getTotalAntesDescuentoAttribute()
    {
        return $this->precio_unitario_antes_descuento * $this->concepto->cantidad_presupuestada;
    }

    public function getTotalAntesDescuentoFormatAttribute()
    {
        return "$".number_format($this->total_antes_descuento, 2, '.', ',');
    }

    public function getPrecioUnitarioDespuesDescuentoAttribute()
    {
        return $this->precio_unitario_moneda_original ;
    }

    public function getPrecioUnitarioDespuesDescuentoFormatAttribute()
    {
        return "$".number_format($this->precio_unitario_despues_descuento, 2, '.', ',');
    }

    public function getTotalDespuesDescuentoAttribute()
    {
        return $this->precio_unitario_despues_descuento * $this->concepto->cantidad_presupuestada;
    }

    public function getTotalDespuesDescuentoFormatAttribute()
    {
        return "$".number_format($this->total_despues_descuento, 2, '.', ',');
    }

    public function getPrecioUnitarioDespuesDescuentoPartidaMCAttribute()
    {
        /*switch ($this->IdMoneda) {
            case (1):
                return $this->precio_unitario;
                break;
            case (2):
                return $this->precio_unitario * $this->presupuesto->dolar;
                break;
            case (3):
                return $this->precio_unitario * $this->presupuesto->euro;
                break;
            case (4):
                return $this->precio_unitario * $this->presupuesto->libra;
                break;
        }*/
        /*Se devuelve el precio unitario guardado en la tabla de la base de datos porque ya se guarda en la moneda de
        conversion con descuento aplicado*/
        return $this->precio_unitario;
    }

    public function getPrecioUnitarioDespuesDescuentoPartidaMCFormatAttribute()
    {
        return "$".number_format($this->precio_unitario_despues_descuento_partida_mc, 2, '.', ',');
    }

    public function getTotalDespuesDescuentoPartidaMCAttribute()
    {
        return $this->precio_unitario_despues_descuento_partida_mc * $this->concepto->cantidad_presupuestada;
    }

    public function getTotalDespuesDescuentoPartidaMCFormatAttribute()
    {
        return "$".number_format($this->total_despues_descuento_partida_mc, 2, '.', ',');
    }

    public function getImporteAttribute()
    {
        return $this->precio_unitario * $this->concepto->cantidad_presupuestada;
    }

    public function getImporteMonedaOriginalAttribute()
    {
        return $this->precio_unitario_moneda_original * $this->concepto->cantidad_presupuestada;
    }

    public function getImporteMonedaOriginalDespuesDescuentoGlobalAttribute()
    {
        return ($this->precio_unitario_moneda_original -($this->precio_unitario_moneda_original * $this->presupuesto->PorcentajeDescuento / 100)) * $this->concepto->cantidad_presupuestada;
    }

    public function scopeCotizadas($query)
    {
        return $query->where('no_cotizado', '=', 0);
    }

    public function getTipoCambioAttribute()
    {
        switch($this->IdMoneda)
        {
            case(1):
                return 1;
                break;
            case(2):
                return $this->presupuesto->dolar;
                break;
            case(3):
                return $this->presupuesto->euro;
                break;
            case(4):
                return $this->presupuesto->libra;
                break;
        }
    }

}
