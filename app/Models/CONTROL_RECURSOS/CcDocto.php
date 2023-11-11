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
}
