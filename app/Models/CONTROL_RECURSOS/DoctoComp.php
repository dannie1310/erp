<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
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

    public function tipoDocComp()
    {
        return $this->belongsTo(TipoDoctoComp::class, 'TipoDoctoComp', 'IdTipoDoctoComp');
    }

    public function tipoGasto()
    {
        return $this->belongsTo(TipoGastoComp::class , 'Concepto', 'IdTipoGastoComp');
    }

    public function cfd()
    {
        return $this->belongsTo(FacturaRepositorio::class, 'IdDoctoComp','id_doc_comprobacion_gastos_cr');
    }

    /**
     * Scopes
     */
    public function scopeDeducibles($query)
    {
        return $query->where('Clasificacion', '=', 1)->orderBy('FechaDocto');
    }

    public function scopeNoDeducibles($query)
    {
        return $query->where('Clasificacion', '=', 2)->orderBy('FechaDocto');
    }

    /**
     * Atributos
     */
    public function getFechaEditarAttribute()
    {
        $date = date_create($this->FechaDocto);
        return date_format($date, "m/d/Y");
    }

    public function getTipoDoctoDescripcionAttribute()
    {
        try {
            return $this->tipoDocComp->DescripcionL;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTipoGastoDescripcionAttribute()
    {
        try {
            return $this->tipoGasto->Descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->FechaDocto);
        return date_format($date,"d/m/Y");
    }

    public function getFechaFormatoAttribute()
    {
        $date = date_create($this->FechaDocto);
        return date_format($date,"d-m-Y");
    }

    public function getConceptoXmlAttribute()
    {
        try {
            return $this->cfd->cfdiSAT->conceptos_txt;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Métodos
     */
}
