<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class DoctoComp extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'doctoscomp';
    protected $primaryKey = 'IdDoctoComp';
    public $timestamps = false;
    protected $fillable = [
        'IdComprobacion',
        'FechaDocto',
        'TipoDoctoComp',
        'FolioDocto',
        'Concepto',
        'Importe',
        'IVA',
        'ImpHosp',
        'Total',
        'Observaciones',
        'Clasificacion',
        'SerieVD',
        'FolioVD',
        'Registro',
        'uuid',
        'descuento_cfdi'
    ];

    /**
     * Relaciones
     */
    public function comprobacion()
    {
        return $this->belongsTo(Comprobacion::class, 'IdComprobacion', 'IdComprobacion');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getFechaEditarAttribute()
    {
        $date = date_create($this->FechaDocto);
        return date_format($date, "m/d/Y");
    }


    /**
     * Métodos
     */
}
