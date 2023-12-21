<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaJuegoXSolicitante extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_juegos_x_solicitante';
    protected $primaryKey = 'idfirmas_firmantes';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function firmaFirmante()
    {
        return $this->belongsTo(FirmaFirmante::class, 'idfirmas_firmantes','idfirmas_firmantes');
    }

    /**
     * Scopes
     */
    public function scopeFirmaActiva($query)
    {
        return $query->whereHas('firmaFirmante', function ($q){
            $q->activo();
        });
    }
}
