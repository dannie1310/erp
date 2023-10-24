<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoDocumentoEliminado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_documentos_eliminados';
    protected $primaryKey = 'idrelaciones_gastos_documentos_eliminados';
    public $timestamps = false;

    protected $fillable = [
        'idrelaciones_gastos_documentos',
        'idrelaciones_gastos',
        'fecha',
        'folio',
        'idtipo_docto_comp',
        'idtipo_gasto_comprobacion',
        'no_personas',
        'importe',
        'iva',
        'retenciones',
        'otros_impuestos',
        'total',
        'observaciones',
        'idestado',
        'registro',
        'timestamp_registro',
        'uuid'
    ];
}
