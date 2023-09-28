<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoDocumentoEstado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_documentos_estados';
    protected $primaryKey = 'idrelaciones_gastos_documentos_estados';

    public $timestamps = false;
    protected $fillable = [
        'idrelaciones_gastos_documentos',
        'idctg_estados_relaciones_documentos',
        'registro'
    ];

    /**
     * Relaciones
     */
    public function estado()
    {
        return $this->belongsTo(CtgEstadoRelacionDocumento::class,'idctg_estados_relaciones_documentos','idestado');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
