<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoFirma extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_firmas';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('encabezado', function ($q) {
              return $q->orderBy('orden', 'asc');
            });
        });
    }

    public function encabezado()
    {
        return $this->belongsTo(FirmaEncabezado::class, 'idfirmas_encabezados', 'idfirmas_encabezados');
    }

    public function firmante()
    {
        return $this->belongsTo(FirmaFirmante::class, 'idfirmas_firmantes', 'idfirmas_firmantes');
    }
}
