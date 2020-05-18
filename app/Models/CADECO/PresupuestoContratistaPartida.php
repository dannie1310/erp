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

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ '. number_format($this->precio_unitario, 2, '.', ',');
    }

    public function getPrecioTotalAttribute()
    {
        return ($this->concepto) ? '$ '. number_format(($this->concepto->cantidad_presupuestada * $this->precio_unitario), 2, '.', ',') : $this->precio_unitario_format; 
    }
}