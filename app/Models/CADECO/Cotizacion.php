<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Moneda;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Compras\CotizacionComplementoPartida;

class Cotizacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cotizaciones';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_material',
        'precio_unitario',
        'cantidad',
        'descuento',
        'anticipo',
        'disponibles',
        'id_moneda',
        'no_cotizado'
    ];


    public function partida(){
        return $this->belongsTo(CotizacionComplementoPartida::class,'id_transaccion', 'id_transaccion')->where('id_material', '=', $this->id_material);
    }
    public function material(){
        return $this->belongsTo(Material::class,'id_material', 'id_material');
    }

    public function cotizacion()
    {
        return $this->hasOne(CotizacionCompra::class, 'id_transaccion');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 1, '.', ',');
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ '. number_format($this->precio_unitario, 2, '.', ',');
    }    

    public function getPrecioTotalAttribute()
    {
        return '$ '. number_format(($this->cantidad * $this->precio_unitario), 2, '.', ',');
    }

    public function getPrecioTotalMonedaAttribute()
    {
        switch ($this->id_moneda)
        {
            case (1):
                return '$ '. number_format(($this->cantidad * $this->precio_unitario), 2, '.', ',');
                break;
            case (2):
                return ($this->cotizacion->complemento) ? '$ '. number_format(($this->cantidad * $this->precio_unitario * $this->cotizacion->complemento->tc_usd), 2, '.', ',') : '---------';
                break;
            case (3):
                return ($this->cotizacion->complemento) ? '$ '. number_format(($this->cantidad * $this->precio_unitario * $this->cotizacion->complemento->tc_eur), 2, '.', ',') : '---------';
                break;
        }
    }
}