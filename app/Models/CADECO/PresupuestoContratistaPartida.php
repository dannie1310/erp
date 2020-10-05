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
            case(4):
                return '$ '. number_format(($this->precio_unitario) / $this->presupuesto->TcLibra, 2, '.', ',');
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
            case(4):
                return ($this->precio_unitario / $this->presupuesto->TcLibra);
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
            case(4):
                return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->TcLibra)) - ((($this->precio_unitario) / $this->presupuesto->TcLibra) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0), 2, '.', ',') : $this->precio_unitario_format;
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
            case(4):
                return ($this->concepto) ? '$ '. number_format((($this->concepto->cantidad_presupuestada * (($this->precio_unitario) / $this->presupuesto->TcLibra)) - ((($this->precio_unitario) / $this->presupuesto->TcLibra) * ($this->PorcentajeDescuento > 0) ? $this->PorcentajeDescuento : 0)) * ($this->presupuesto->TcLibra), 2, '.', ',') : $this->precio_unitario_format;
            break;
        }
    }
}