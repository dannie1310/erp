<?php


namespace App\Models\CADECO;


class ItemComprobanteFondo extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_concepto',
        'cantidad',
        'importe',
        'referencia',
        'estado'
    ];


    /**
     * Relaciones Eloquent
     */
    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto','id_concepto');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */
    public function getImporteFormatAttribute()
    {
        return '$ ' .  number_format($this->importe,2,'.', ',');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,2,'.', ',');
    }

    public function getMontoAttribute()
    {
        return $this->cantidad * $this->importe;
    }

    public function getMontoFormatAttribute()
    {
        return number_format($this->monto,2,'.', ',');
    }

    /**
     * Métodos
     */
}
