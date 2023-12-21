<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\TipoCambio;
use Illuminate\Database\Eloquent\Model;

class SolCheque extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solcheques';
    protected $primaryKey = 'IdSolCheques';
    public $timestamps = false;

    protected $fillable = [
        'Folio',
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

    public function firmasSolicitantes()
    {
        return $this->hasMany(FirmaSolicitud::class, 'idsolcheque', 'IdSolCheques');
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

    public function getIvaFormatAttribute()
    {
        return '$' . number_format(($this->IVA),2);
    }

    public function getOtrosFormatAttribute()
    {
        return '$' . number_format(($this->OtrosImpuestos),2);
    }

    public function getRetencionesFormatAttribute()
    {
        return '$' . number_format(($this->Retenciones),2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFolioCompuestoAttribute()
    {
        return $this->Serie.' - '.$this->Folio;
    }

    public function getFechaVencimientoFormatAttribute()
    {
        $date = date_create($this->Vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getIdSolicitudAttribute()
    {
        try {
            return $this->relacionGastoXDocumento->solicitudCheque->IdSolCheque;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
    public function getFolio($serie)
    {
        $solicitud = self::where('Serie', $serie)->orderBy('Folio', 'desc')->first();
        return $solicitud ? $solicitud->Folio + 1 : 1;
    }

    public function setFirmasSolicitantes($solicitante)
    {
        if($this->Serie == 'CDM')
        {
            $this->firmasSolicitantes->create([
                'idsolcheque' => $this->getKey(),
                'idfirmas_encabezados' => 1,
                'idfirmas_firmantes' => 2
                ],
                [
                    'idsolcheque' => $this->getKey(),
                    'idfirmas_encabezados' => 6,
                    'idfirmas_firmantes' => 22
                ],
                [
                    'idsolcheque' => $this->getKey(),
                    'idfirmas_encabezados' => 7,
                    'idfirmas_firmantes' => 35
                ]);
        }
        else {
            $firmas = FirmaJuegoXSolicitante::firmaActiva()->where('idsolicita', $solicitante)->get();

            foreach ($firmas as $firma) {
                $this->firmasSolicitantes()->create([
                    'idsolcheque' => $this->getKey(),
                    'idfirmas_encabezados' => $firma->idfirmas_encabezados,
                    'idfirmas_firmantes' => $firma->idfirmas_firmantes
                ]);
            }

            if ($this->Serie != 'CLT' && $this->Serie != 'CLS')
            {
                $count_firma = FirmaJuegoXSolicitante::firmaActiva()->where('idsolicita', $solicitante)
                    ->where('idfirmas_firmantes', 22)->count();

                $count_firma2 = FirmaJuegoXSolicitante::firmaActiva()->where('idsolicita', $solicitante)
                    ->where('idfirmas_firmantes', 70)->count();

                $total = $this->Total;
                if($this->IdMoneda ==  1)
                {
                    $USD = TipoCambio::where('moneda', 1)->where('fecha', date('Y-m-d'))->first();
                    $total = $this->Total * $USD->tipo_cambio;
                }
                if($this->IdMoneda ==  2)
                {
                    $EUR = TipoCambio::where('moneda', 2)->where('fecha', date('Y-m-d'))->first();
                    $total = $this->Total * $EUR->tipo_cambio;
                }

                if($count_firma == 0 && $total >= 50000)
                {
                    $this->firmasSolicitantes()->create([
                        'idsolcheque' => $this->getKey(),
                        'idfirmas_encabezados' => 3,
                        'idfirmas_firmantes' => 22
                    ]);
                }

                if($count_firma2 == 0 && $total >= 100000)
                {
                    $this->firmasSolicitantes()->create([
                        'idsolcheque' => $this->getKey(),
                        'idfirmas_encabezados' => 4,
                        'idfirmas_firmantes' => 713
                    ]);
                }
            }
        }
    }
}
