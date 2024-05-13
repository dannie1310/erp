<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\TipoCambio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        return $this->belongsTo(SolChequeDocto::class, 'IdSolCheques', 'IdSolCheque');
    }

    public function firmasSolicitantes()
    {
        return $this->hasMany(FirmaSolicitud::class, 'idsolcheque', 'IdSolCheques');
    }

    public function ccSolCheques()
    {
        return $this->hasMany(CcSolCheque::class, 'IdSolCheque','IdSolCheques');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'IdFormaPago', 'IdFormaPago');
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class, 'IdTipoPago', 'IdTipoPago');
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'IdEntrega', 'IdEntrega');
    }

    public function estado()
    {
        return $this->belongsTo(EstatusSolicitud::class, 'Estatus', 'IDestatus');
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

    public function getFirmaSolicitanteAttribute()
    {
        try {
            return $this->firmasSolicitantes[0]->idfirmas_firmantes;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getIdRelacionAttribute()
    {
        try {
            return $this->solChequeDocto->documento->relacionXDocumento->idrelaciones_gastos;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getRelacionAttribute()
    {
        try {
            return $this->solChequeDocto->documento->relacionXDocumento->relacion;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPartidasParaPdfAttribute()
    {
        return DB::connection('controlrec')->select(DB::raw(
            "SELECT if(ccdoctos.Facturable='Y','F','-')as Facturable,
                   if(centroscosto.NoSN is null,' - ',centroscosto.NoSN) as NS,
                   count(centroscosto.Descripcion) as TotalCC,
                   centroscosto.Descripcion AS `SN`,
                   tiposgasto.Descripcion AS `TG`,
                   documentos.Importe AS `ImporteDocumento`,
                   documentos.Retenciones AS `RetencionesDocumento`,
                documentos.OtrosImpuestos AS `OtrosImpuestosDocumento`,
                   documentos.TasaIVA,
                   documentos.IVA AS `IVADocumento`,
                   documentos.Total AS `TotalDocumento`,
                   FORMAT(SUM(ccdoctos.Importe),3) AS `ImporteSN`,
                   FORMAT(SUM(ccdoctos.IVA),3) as `IVASN`,
                   FORMAT(SUM(ccdoctos.Retenciones),3) as `RetencionesSN`,
                   FORMAT(SUM(ccdoctos.OtrosImpuestos),3) as `OtrosImpuestos`,
                   FORMAT(SUM(ccdoctos.Total),3) as `TotalSN`
              FROM    (   (   (   (   controlrec.ccdoctos ccdoctos
                                   INNER JOIN
                                      controlrec.centroscosto centroscosto
                                   ON (ccdoctos.IdCC = centroscosto.IdCC))
                               INNER JOIN
                                  controlrec.documentos documentos
                               ON (ccdoctos.IdDocto = documentos.IdDocto))
                           INNER JOIN
                              controlrec.solchequesdoctos solchequesdoctos
                           ON (solchequesdoctos.IdDocto = documentos.IdDocto))
                       INNER JOIN
                          controlrec.solcheques solcheques
                       ON (solchequesdoctos.IdSolCheque = solcheques.IdSolCheques))
                   INNER JOIN
                      controlrec.tiposgasto tiposgasto
                   ON (ccdoctos.IdTipoGasto = tiposgasto.IdTipoGasto)
                   left join segmentos_negocio_contabilidad as SNC on(centroscosto.NoSN=SNC.NumeroSegmento)
             WHERE (solcheques.IdSolCheques = " . $this->getKey() . ") GROUP BY  centroscosto.Descripcion,tiposgasto.Descripcion,ccdoctos.Facturable,centroscosto.NoSN,documentos.Importe,
              documentos.Retenciones,documentos.OtrosImpuestos,documentos.TasaIVA,documentos.IVA,documentos.Total;"));
    }

    public function getUuidsAttribute()
    {
        $array = [];
        $x=0;
        foreach($this->solChequeDocto->documento->relacionXDocumento->relacion->documentos as $doc){
            if($doc->uuid != null)
            {
                $array[$x] = $doc;
                $x++;
            }
        }
       return $array;
    }

    public function getDescripcionEstadoAttribute()
    {
        try {
            return $this->estado->Descripcion;
        } catch (\Exception $e)
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

    public function updateFirmasSolicitantes($solicitante)
    {
        if($solicitante != $this->firmasSolicitantes[0]->idfirmas_firmantes)
        {
            $this->deleteFirmasSolicitantes();
            $this->setFirmasSolicitantes($solicitante);
        }
    }

    public function deleteFirmasSolicitantes()
    {
        $this->firmasSolicitantes()->delete();
    }
}
