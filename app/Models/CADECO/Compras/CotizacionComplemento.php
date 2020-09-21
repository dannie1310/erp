<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CotizacionComplemento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.cotizacion_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'parcialidades',
        'dias_credito',
        'vigencia',
        'descuento',
        'plazo_entrega',
        'anticipo',
        'importe',
        'tc_usd',
        'tc_eur',
        'registro',
        'timestamp_registro'
    ];

    public function getTipoCambioUsdFormatAttribute()
    {
        return '$ ' . number_format($this->tc_usd, 4, '.', ',');
    }

    public function getTipoCambioEurFormatAttribute()
    {
        return '$ ' . number_format($this->tc_eur, 4, '.', ',');
    }

    public function getTipoCambioLibFormatAttribute()
    {
        return '$ ' . number_format($this->tc_libra, 4, '.', ',');
    }

    public function getDescuentoFormatAttribute()
    {
        return number_format($this->descuento, 2, '.', ',') . ' %';
    }

    public function getParcialidadesFormatAttribute()
    {
        return number_format($this->parcialidades, 2, '.', ',') . ' %';
    }

    public function getAnticipoFormatAttribute()
    {
        return number_format($this->anticipo, 2, '.', ',') . ' %';
    }
}