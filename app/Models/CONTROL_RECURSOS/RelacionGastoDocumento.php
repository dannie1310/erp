<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoDocumento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_documentos';
    protected $primaryKey = 'idrelaciones_gastos_documentos';
    public $timestamps = false;

    protected $fillable = [
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
        'modifico_estado',
        'registro',
        'uuid'
    ];

    /**
     * Relaciones
     */
    public function estados()
    {
        return $this->hasMany(RelacionGastoDocumentoEstado::class,'idrelaciones_gastos_documentos','idrelaciones_gastos_documentos');
    }

    public function relacion()
    {
        return $this->belongsTo(RelacionGasto::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }


    /**
     * Métodos
     */
    /**
     * Se realiza la función para agregar los estados a tablas adicionales, pero ya se realiza por medio de SP
     */
    public function agregarEstados()
    {
        $this->estados()->create([
            'idrelaciones_gastos_documentos' => $this->getKey(),
            'idctg_estados_relaciones_documentos' => $this->idestado,
            'registro' => auth()->id()
        ]);
    }
}
