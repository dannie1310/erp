<?php


namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Moneda;
use App\Models\IGH\TipoCambio;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Compras\CotizacionComplementoPartida;

class CotizacionCompraPartida extends Model
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

    public function partida()
    {
        return $this->belongsTo(CotizacionComplementoPartida::class,'id_transaccion', 'id_transaccion')->where('id_material', '=', $this->id_material);
    }

    public function material()
    {
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

    public function getItemSolicitudAttribute(){
        if(!is_null(Context::getIdObra())) {
            $item = Item::where('id_transaccion', '=', $this->cotizacion->id_antecedente)->where('id_material', '=', $this->id_material)->first();
            return $item->id_item;
        }
        return null;
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
        if(!is_null(Context::getIdObra()))
        {
            $cotizacion = $this->cotizacion;
        }else{
            $cotizacion = CotizacionCompra::withoutGlobalScopes()->where('id_transaccion', $this->id_transaccion)->first();
        }
        switch ($this->id_moneda)
        {
            case (1):
                return '$ '. number_format(($this->cantidad * $this->precio_unitario), 2, '.', ',');
                break;
            case (2):
                return ($cotizacion->complemento) ? '$ '. number_format(($this->cantidad * $this->precio_unitario * $cotizacion->complemento->tc_usd), 2, '.', ',') : '---------';
                break;
            case (3):
                return ($cotizacion->complemento) ? '$ '. number_format(($this->cantidad * $this->precio_unitario * $cotizacion->complemento->tc_eur), 2, '.', ',') : '---------';
                break;
            case (4):
                return  '$ '. number_format(($this->cantidad * $this->precio_unitario * $this->tipo_cambio), 2, '.', ',');
                break;
        }
    }

    public function getTotalPrecioMonedaAttribute()
    {
        switch ($this->id_moneda)
        {
            case (1):
                return $this->cantidad * $this->precio_compuesto;
                break;
            case (2):
                return($this->cotizacion->complemento) ? $this->cantidad * $this->precio_compuesto * $this->cotizacion->complemento->tc_usd : $this->cantidad * $this->precio_compuesto * $this->tipo_cambio;
                break;
            case (3):
                return ($this->cotizacion->complemento) ? $this->cantidad * $this->precio_compuesto * $this->cotizacion->complemento->tc_eur : $this->cantidad * $this->precio_compuesto * $this->tipo_cambio;
                break;
            case (4):
                return $this->cantidad * $this->precio_compuesto * $this->tipo_cambio;
                break;
        }
    }

    public function getPrecioUnitarioCompuestoAttribute()
    {
        switch ($this->id_moneda)
        {
            case (1):
                return $this->precio_compuesto;
                break;
            case (2):
                return ($this->cotizacion->complemento) ? $this->precio_compuesto * $this->cotizacion->complemento->tc_usd : $this->precio_compuesto * $this->tipo_cambio;
                break;
            case (3):
                return ($this->cotizacion->complemento) ? $this->precio_compuesto * $this->cotizacion->complemento->tc_eur : $this->precio_compuesto * $this->tipo_cambio;
                break;
            case (4):
                return ($this->cotizacion->complemento) ? $this->precio_compuesto * $this->cotizacion->complemento->tc_libra : $this->precio_compuesto * $this->tipo_cambio;
                break;
        }
    }

    public function getPrecioCompuestoAttribute()
    {
        return $this->descuento != 0 ? $this->precio_unitario - ($this->precio_unitario * $this->descuento / 100) : $this->precio_unitario;
    }

    public function getPrecioCompuestoTotalAttribute()
    {
        return $this->precio_compuesto * $this->cantidad;
    }

    public function getTipoCambioAttribute()
    {
        return $this->moneda->cambio ? $this->moneda->cambio->cambio : 1;
    }

    public function getDescripcionMaterialAttribute()
    {
        try{
            return $this->material->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNumeroParteMaterialAttribute()
    {
        try{
            return $this->material->numero_parte;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getUnidadMaterialAttribute()
    {
        try{
            return $this->material->unidad;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getMonedaNombreAttribute()
    {
        try{
            return $this->moneda->nombre;
        }catch (\Exception $e){
            return null;
        }
    }
}
