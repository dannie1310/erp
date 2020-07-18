<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\Material;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\CotizacionCompra;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ItemSolicitudCompra;
use App\Models\CADECO\CotizacionCompraPartida;
use App\Models\CADECO\Compras\AsignacionProveedor;

class AsignacionProveedorPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table      = 'Compras.asignacion_proveedores_partidas';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_asignacion_proveedores',
        'id_transaccion_cotizacion',
        'id_item_solicitud',
        'id_material',
        'cantidad_asignada',
        'registro',
    ];

    protected static function boot ()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->registro           = auth()->id();
            $model->timestamp_registro = date('Y-m-d h:i');
        });
    }

    public function asignacion()
    {
        return $this->belongsTo(AsignacionProveedor::class, 'id_asignacion_proveedores', 'id');
    }

    public function cotizacion()
    {
        return $this->belongsTo(CotizacionCompraPartida::class, 'id_transaccion_cotizacion', 'id_transaccion')->where('id_material', '=', $this->id_material);
    }

    public function cotizacionCompra()
    {
        return $this->belongsTo(CotizacionCompra::class, 'id_transaccion_cotizacion', 'id_transaccion');
    }

    public function itemSolicitud()
    {
        return $this->belongsTo(ItemSolicitudCompra::class, 'id_item_solicitud', 'id_item');
    }

    public function ordenCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'id_referente', 'id_transaccion_cotizacion');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function getCantidadFormatAttribute(){
        return number_format($this->cantidad_asignada, 4, '.', ',');
    }

    public function getCantidadAsignadaFormatAttribute()
    {
        return number_format($this->cantidad_asignada, 1, '.', ',');
    }

    public function getConOrdenCompraAttribute(){
        return $this->cotizacion?$this->ordenCompra()->where('id_moneda', '=', $this->cotizacion->id_moneda)->count() > 0:0;
    }

    public function getTotalPrecioMonedaAttribute()
    {
        switch ($this->cotizacion->id_moneda)
        {
            case (1):
                return $this->cantidad * $this->cotizacion->precio_compuesto;
                break;
            case (2):
                return($this->cotizacionCompra->complemento) ? ($this->cantidad_asignada * $this->cotizacion->precio_compuesto * $this->cotizacionCompra->complemento->tc_usd) : ($this->cantidad_asignada * $this->cotizacion->precio_compuesto * $this->tipo_cambio(1));
                break;
            case (3):
                return ($this->cotizacionCompra->complemento) ? $this->cantidad_asignada * $this->cotizacion->precio_compuesto * $this->cotizacionCompra->complemento->tc_eur : $this->cantidad_asignada * $this->cotizacion->precio_compuesto * $this->tipo_cambio(2);
                break;
        }
    }
}
