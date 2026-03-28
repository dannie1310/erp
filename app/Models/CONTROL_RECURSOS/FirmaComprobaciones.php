<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaComprobaciones extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_comprobaciones';

    /**
     * Relaciones
     */
    public function encabezado()
    {
        return $this->belongsTo(FirmaEncabezado::class,'idfirmas_encabezados','idfirmas_encabezados');
    }

    public function firmante()
    {
        return $this->belongsTo(FirmaFirmante::class, 'idfirmas_firmantes', 'idfirmas_firmantes');
    }

    /**
     * Scopes
     */
    public function scopePorOrden($query)
    {
        return $query->join('firmas_encabezados', 'firmas_encabezados.idfirmas_encabezados','firmas_comprobaciones.idfirmas_encabezados')
            ->orderBy('orden', 'asc');
    }
}
