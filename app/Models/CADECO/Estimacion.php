<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 04:24 PM
 */

namespace App\Models\CADECO;
use App\Facades\Context;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;
use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;

class Estimacion extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 52);
        });

        self::creating(function ($estimacion) {
            $subcontrato = Subcontrato::find($estimacion->id_antecedente);
            $estimacion->tipo_transaccion = 52;
            $estimacion->id_empresa = $subcontrato->id_empresa;
            $estimacion->id_moneda = $subcontrato->id_moneda;
            $estimacion->opciones = 0;
            $estimacion->saldo = $estimacion->monto;
            $estimacion->retencion = $subcontrato->retencion;
        });
        self::created(function ($estimacion) {
            if ($estimacion->retencion > 0) {
                $estimacion->generaRetencion();
            }
        });
    }

    public function retencion_fondo_garantia()
    {
        return $this->hasOne(RetencionFondoGarantia::class,'id_estimacion','id_transaccion');
    }

    public function generaRetencion()
    {
        if(is_null($this->retencion_fondo_garantia))
        {
            if ($this->retencion > 0) {
                $retencion_fondo_garantia = new RetencionFondoGarantia();
                $retencion_fondo_garantia->id_estimacion = $this->id_transaccion;
                $retencion_fondo_garantia->importe = $this->importe_retencion;
                $retencion_fondo_garantia->usuario_registra = $this->usuario_registra;
                $retencion_fondo_garantia->save();
                $this->refresh();
            } else {
                throw New \Exception('La estimación no tiene establecido un porcentaje de retención de fondo de garantía, la retención no puede generarse');
            }
        }
    }

    public function subcontrato()
    {
        # return $this->belongsTo(Subcontrato::class,'id_transaccion', 'id_antecedente');
        return $this->hasOne(Subcontrato::class, 'id_transaccion', 'id_antecedente');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'id_transaccion', 'id_transaccion');
    }

    public function liberaciones()
    {
        return $this->hasMany(Liberacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function getImporteRetencionAttribute()
    {
        return $this->monto*$this->retencion /100;
    }

    public function subcontratoEstimacion()
    {
        return $this->hasOne(\App\Models\CADECO\SubcontratosEstimaciones\Estimacion::class, 'IDEstimacion', 'id_transaccion');
    }

    public function retenciones()
    {
        return $this->hasMany(Retencion::class, 'id_transaccion', 'id_transaccion');
    }

    public function getSumaImportesAttribute()
    {
        return $this->items()->sum('importe');
    }

    public function getAmortizacionPendienteAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();
        return $this->subcontrato->anticipo_monto - $estimaciones_anteriores->sum('monto_anticipo_aplicado') - $this->monto_anticipo_aplicado;
    }

    public function getAmortizacionPendienteAnteriorAttribute()
    {
        $estimacion_anterior = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->orderBy('id_transaccion', 'DESC')->first();
        if ($estimacion_anterior) {
            return $estimacion_anterior->amortizacion_pendiente;
        } else {
            return 0;
        }
    }

    public function getMontoAnticipoAplicadoAttribute()
    {
        return $this->suma_importes * ($this->anticipo / 100);
    }

    public function getRetenidoAnteriorAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->SumMontoRetencion;
        }
        return $sumatoria;
    }

    public function getRetenidoOrigenAttribute()
    {
        $estimaciones_anteriores = $this->subcontrato->estimaciones()->where('id_transaccion', '<', $this->id_transaccion)->get();

        $sumatoria = 0;
        foreach ($estimaciones_anteriores as $estimacion) {
            $sumatoria += $estimacion->SumMontoRetencion;
        }
        return $sumatoria + $this->SumMontoRetencion;
    }

    public function getMontoAPagarAttribute()
    {
        return (
            $this->monto

            - ($this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteFondoGarantia : 0)
            - (!in_array(Context::getDatabase(),['SAO1814_TERMINAL_NAICM', 'SAO1814_DEV_TERMINAL_NAICM']) ? $this->descuentos->sum('importe') : 0)
            - $this->retenciones->sum('importe')
            - $this->IVARetenido
            + $this->liberaciones->sum('importe')
            + ($this->subcontratoEstimacion ? $this->subcontratoEstimacion->ImporteAnticipoLiberar : 0)
        );
    }
}