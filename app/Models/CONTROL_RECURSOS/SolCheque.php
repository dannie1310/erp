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
        'Fecha'
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
    public function registrar($datos)
    {
        try {
            $reembolso = ReembolsoGastoSol::where('IdDocto',$datos['reembolso']['id'])->first();

            if($reembolso->Estatus == 11)
            {
                abort(500, "Este reembolso ya se encuentra asociado a una solicitud.");
            }

            $fecha = new DateTime($datos['reembolso']['fecha_inicio_editar']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_fin = new DateTime($datos['reembolso']['fecha_final_editar']);
            $fecha_fin->setTimezone(new DateTimeZone('America/Mexico_City'));

            DB::connection('controlrec')->beginTransaction();

            $sol_cheque = $this->create([
                'Fecha' => date('Y-m-d'),
                'Vencimiento' => $fecha_fin->format('Y-m-d'),
                'IdEmpresa' => $reembolso->IdEmpresa,
                'IdProveedor' => $reembolso->IdProveedor,
                'IdMoneda' => $reembolso->IdMoneda,
                'Importe' => $reembolso->Importe,
                'Retenciones' => $reembolso->Retenciones,
                'IVA' => $reembolso->IVA,
                'OtrosImpuestos' => $reembolso->OtrosImpuestos,
                'Total' => $reembolso->Total,
                'Concepto' => $datos['reembolso']['motivo'],
                'Estatus' => 30,
                'IdTipoSolicitud' => 4,
                'IdFormaPago' => $datos['forma_pago'],
                'IdTipoPago' => 6,
                'IdEntrega' => $datos['instruccion'],
                'Cuenta2' => $datos['cuenta'],
                'IdSerie' => $reembolso->IdSerie,
                'Serie' => $reembolso->Alias_Depto,
                'IdGenero' => auth()->id(),
                'FechaFactura' =>  $fecha->format("Y-m-d"),
            ]);

            foreach ($reembolso->ccDoctos as $docto)
            {
                CcSolCheque::create([
                    'IdCCDoctos' => $docto->IdCCDoctos,
                    'IdSolCheque' => $sol_cheque->getKey(),
                    'IdCC' => $docto->IdCC,
                    'IdTipoGasto' => $docto->IdTipoGasto,
                    'Importe' => $docto->Importe,
                    'IVA' => $docto->IVA,
                    'OtrosImpuestos' => $docto->OtrosImpuestos,
                    'Retenciones' => $docto->Retenciones,
                    'Total' => $docto->Total,
                    'PorcentajeFacturar' => $docto->PorcentajeFacturar,
                    'ImporteFacturar' => $docto->ImporteFacturar,
                    'Facturable' => $docto->Facturable
                ]);
            }

            SolChequeDocto::create([
                'IdSolCheque' => $sol_cheque->getKey(),
                'IdDocto' => $reembolso->getKey()
            ]);

            $reembolso->update([
               'Estatus'  => 13
            ]);
            DB::connection('controlrec')->commit();
            return $sol_cheque;

        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}
