<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
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

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDoctoComp::class, 'idtipo_docto_comp','IdTipoDoctoComp');
    }

    public function tipoGasto()
    {
        return $this->belongsTo(TipoGastoComp::class, 'idtipo_gasto_comprobacion','IdTipoGastoComp');
    }

    public function estado()
    {
        return $this->belongsTo(CtgEstadoRelacionDocumento::class, 'idestado','idctg_estados_relaciones_documentos');
    }

    public function cfd()
    {
        return $this->belongsTo(FacturaRepositorio::class, 'idrelaciones_gastos_documentos','id_doc_relacion_gastos_cr');
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

    public function getFechaEditarAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date, "m/d/Y");
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->total),2);
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->importe),2);
    }

    public function getIvaFormatAttribute()
    {
        return '$' . number_format(($this->iva),2);
    }

    public function getRetencionesFormatAttribute()
    {
        return '$' . number_format(($this->retenciones),2);
    }

    public function getOtrosImpFormatAttribute()
    {
        return '$' . number_format($this->otros_impuestos,2);
    }

    public function getEstatusDescripcionAttribute()
    {
        try {
            return $this->estado->descripcion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->idestado) {
            case 1:
                return '#EEE416';
            case 2:
                return '#EE9916';
            case 3:
                return '#EE5416';
            case 4:
                return '#5D9B23';
            case 5:
                return '#00a65a';
            case 6:
                return '#237A9B';
            case 60:
                return '#B54AC2';
            case 600:
                return '#E2B029';
            default:
                return '#d1cfd1';
        }
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
