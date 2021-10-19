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

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query)
        {
            return $query->whereHas('avanceObra');
        });
    }


    /**
     * Relaciones
     */
    public function avanceObra()
    {
        return $this->belongsTo(AvanceObra::class, 'id_transaccion','id_transaccion');
    }
}
