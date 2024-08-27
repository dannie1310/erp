<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaSolicitud extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmas_solicitudes';
    public $timestamps = false;
    protected $fillable = [
        'idsolcheque',
        'idfirmas_encabezados',
        'idfirmas_firmantes'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('encabezado', function ($q) {
                return $q->orderBy('orden', 'asc');
            });
        });
    }

    /**
     * Relaciones
     */
    public function encabezado()
    {
        return $this->belongsTo(FirmaEncabezado::class, 'idfirmas_encabezados', 'idfirmas_encabezados');
    }

    public function firmante()
    {
        return $this->belongsTo(FirmaFirmante::class, 'idfirmas_firmantes', 'idfirmas_firmantes');
    }
}
