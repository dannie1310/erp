<?php


namespace App\Models\CADECO;


class ItemAvanceObra extends Item
{
    protected $fillable = [
        'id_transaccion',
        'id_concepto',
        'cantidad',
        'precio_unitario',
        'importe',
        'numero'
    ];

    /**
     * Relaciones
     */
    public function avanceObra()
    {
        return $this->hasOne(AvanceObra::class, 'id_transaccion','id_transaccion');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    /**
     * Scopes
     */

    /**
     * Attributos
     */
    public function getCantidadAvanceActualAttribute()
    {
        return number_format($this->cantidad_anterior_avance + $this->cantidad,6, ".","");
    }

    public function getCantidadAvanceActualFormatAttribute()
    {
        return number_format($this->cantidad_avance_actual,4);
    }

    public function getMontoAvanceActualAttribute()
    {
        return (float) $this->cantidad_avance_actual * (float) $this->concepto->precio_produccion;
    }

    public function getMontoAvanceActualFormatAttribute()
    {
        return number_format($this->monto_avance_actual,4);
    }

    public function getCantidadAnteriorAvanceAttribute()
    {
        return ItemAvanceObra::where('id_concepto', $this->id_concepto)->where('id_transaccion','<', $this->id_transaccion)->selectRaw('SUM(cantidad) AS cantidad')->first()->cantidad;
    }

    public function getCantidadAnteriorAvanceFormatAttribute()
    {
        return number_format($this->cantidad_anterior_avance,4);
    }

    public function getMontoAvanceAttribute()
    {
        return (float) $this->cantidad_anterior_avance * (float) $this->concepto->precio_produccion;
    }

    public function getMontoAvanceFormatAttribute()
    {
        return number_format($this->monto_avance,4);
    }

    public function getCumplidoAttribute()
    {
        return $this->numero == 1 ? true : false;
    }

    public function getCantidadFormatAttribute(){
        return number_format($this->cantidad, 6, ".", "");
    }
    
    public function getAvanceObraActivoAttribute()
    {
        if($this->avanceObra)
        {
            if($this->avanceObra->estado == 0)
            {
                return true;
            }
        }
        return false;
    }

    public function getConceptoDescripcionAttribute()
    {
        try{
            return $this->concepto->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Métodos
     */
}
