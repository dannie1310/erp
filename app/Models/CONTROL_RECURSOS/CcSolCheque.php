<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CcSolCheque extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'ccsolcheques';
    protected $primaryKey = 'IdCCSolCheques';
    public $timestamps = false;
    protected $fillable = [
        'IdCCSolCheques',
        'IdCCDoctos',
        'IdSolCheque',
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
}
