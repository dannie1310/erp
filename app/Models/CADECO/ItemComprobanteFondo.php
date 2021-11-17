<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\ComprobanteFondoPartidaEliminada;

class ItemComprobanteFondo extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_concepto',
        'cantidad',
        'importe',
        'referencia',
        'estado',
        'id_material',
        'id_antecedente'
    ];


    /**
     * Relaciones Eloquent
     */
    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto','id_concepto');
    }

    public function partidaRespaldo()
    {
        return $this->belongsTo(ComprobanteFondoPartidaEliminada::class, 'id_item', 'id_item');
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
    public function respaldar()
    {
        ComprobanteFondoPartidaEliminada::create([
            'id_item' => $this->getKey(),
            'id_transaccion' => $this->id_transaccion,
            'id_concepto' => $this->id_concepto,
            'cantidad' => $this->cantidad,
            'importe' => $this->importe,
            'referencia' => $this->referencia,
            'estado' => $this->estado
        ]);
    }
}
