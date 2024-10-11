<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CcDocto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ccdoctos';
    protected $primaryKey = 'IdCCDoctos';
    public $timestamps = false;

    protected $fillable = [
        'IdDocto',
        'IdCC',
        'IdTipoGasto',
        'Importe',
        'IVA',
        'OtrosImpuestos',
        'Retenciones',
        'Total',
        'PorcentajeFacturar',
        'ImporteFacturar',
        'Facturable'
    ];

    /**
     * Relaciones
     */
    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'IdCC', 'IdCC');
    }

    public function tipoGastoComp()
    {
        return $this->belongsTo(TipoGastoComp::class, 'IdTipoGasto','IdTipoGastoComp');
    }

    public function ccSolCheque()
    {
        return $this->belongsTo(CcSolCheque::class, 'IdCCDoctos', 'IdCCDoctos');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->Total),2);
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->Importe),2);
    }

    public function getIvaFormatAttribute()
    {
        return '$' . number_format(($this->IVA),2);
    }

    public function getRetencionesFormatAttribute()
    {
        return '$' . number_format(($this->Retenciones),2);
    }

    public function getOtrosImpuestosFormatAttribute()
    {
        return '$' . number_format(($this->OtrosImpuestos),2);
    }

    public function getCentroCostoDescripcionAttribute()
    {
        try {
            return $this->centroCosto->Descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }
}
