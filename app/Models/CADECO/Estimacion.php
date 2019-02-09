<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/02/2019
 * Time: 04:24 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\SubcontratosFG\RetencionFondoGarantia;

class Estimacion extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'monto',
        'saldo',
        'autorizado',
        'impuesto',
        'anticipo',
        'retencion',
        'referencia',
        'observaciones',

    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($estimacion) {
            $estimacion->tipo_transaccion = 52;
            $estimacion->opciones = 0;
        });
        self::created(function ($estimacion) {
            if ($estimacion->retencion > 0) {
                $estimacion->generaRetencion();
            }
        });
    }

    public function retencion_fondo_garantia()
    {
        return $this->hasOne(RetencionFondoGarantia::class,'id_estimacion');
    }

    public function generaRetencion()
    {
        if(is_null($this->retencion_fondo_garantia))
        {
            if ($this->retencion > 0) {
                $retencion_fondo_garantia = new RetencionFondoGarantia();
                $retencion_fondo_garantia->id_estimacion = $this->id_transaccion;
                $retencion_fondo_garantia->importe = $this->importe_retencion_fg;
                $retencion_fondo_garantia->usuario_registra = $this->usuario_registra;
                $retencion_fondo_garantia->save();
                $this->refresh();
            } else {
                throw New \Exception('La estimación no tiene establecido un porcentaje de retención de fondo de garantía, la retención no puede generarse');
            }
        }
    }

}