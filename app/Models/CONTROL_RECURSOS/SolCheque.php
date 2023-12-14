<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;

class SolCheque extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solcheques';
    protected $primaryKey = 'IdSolCheques';
    public $timestamps = false;

    protected $fillable = [
        'IdSolCheques',
        'FechaFactura',
        'Vencimiento',
        'IdEmpresa',
        'IdProveedor',
        'IdMoneda',
        'Importe',
        'Retenciones',
        'IVA',
        'OtrosImpuestos',
        'Total',
        'Concepto',
        'Estatus',
        'IdTipoSolicitud',
        'IdFormaPago',
        'IdTipoPago',
        'IdEntrega',
        'Cuenta2',
        'Folio',
        'IdSerie',
        'Serie',
        'IdGenero',
        'Fecha',
        'registro_portal'
    ];

    /**
     * Relaciones
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function partidasSolicitudRecursos()
    {
        return $this->hasMany(PartidaSolRec::class, 'IdSolCheque', 'IdSolCheques');
    }

    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class, 'IdMoneda','id');
    }

    public function cuentaProveedor()
    {
        return $this->hasOne(CuentaProveedor::class, 'IdCuenta','Cuenta2');
    }

    public function solChequeDocto()
    {
        return $this->belongsTo(SolChequeDocto::class, 'IdSolCheque', 'IdSolCheques');
    }

    /**
     * Scopes
     */
    public function scopePartidaAutorizada($query)
    {
        return $query->whereHas('partida', function ($q){
            $q->autorizada();
        });
    }

    public function scopePorSemanaAnio($query, $idsemana)
    {
        $time = SolrecSemanaAnio::where('idsemana_anio', $idsemana)->first();
        $solicitudes = SolRecurso::autorizadas()->where('Semana', '=', $time->semana)->where('Anio', $time->anio)->pluck('IdSolRec');
        return $query->whereHas('partidasSolicitudRecursos', function ($q) use ($solicitudes){
            $q->autorizada()->whereIn('IdSolRec', $solicitudes);
        });
    }

    public function scopeOrdenaSerieFolio($query)
    {
        return $query->orderBy('Serie', 'desc')->orderBy('Folio', 'desc');
    }

    public function scopeTransferencia($query)
    {
        return $query->where("IdFormaPago","2");
    }

    /**
     * Atributos
     */
    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->Importe),2);
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->Total),2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    /**
     * Métodos
     */
}
