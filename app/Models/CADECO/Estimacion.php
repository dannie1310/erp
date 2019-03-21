<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:12 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;
use App\Models\CADECO\SubcontratosEstimaciones\Retencion;

class Estimacion extends Transaccion
{
    protected $fillable = ['id_transaccion', 'id_antecedente', 'tipo_transaccion'];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 52);
        });
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'id_transaccion', 'id_transaccion');
    }

    public function liberaciones()
    {
        return $this->hasMany(Liberacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function subcontrato()
    {
        return $this->hasOne(Subcontrato::class, 'id_transaccion', 'id_antecedente');
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