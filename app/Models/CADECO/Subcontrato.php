<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 03:55 PM
 */

namespace App\Models\CADECO;
use App\Models\CADECO\SubcontratosFG\FondoGarantia;

class Subcontrato extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;
    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_empresa',
        'id_moneda',
        'anticipo',
        'anticipo_monto',
        'anticipo_saldo',
        'monto',
        'PorcentajeDescuento',
        'impuesto',
        'impuesto_retenido',
        'id_costo',
        'retencion',
        'referencia',
        'id_obra'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($subcontrato) {
            $subcontrato->tipo_transaccion = 51;
            $subcontrato->opciones = 2;
        });
        self::created(function ($subcontrato) {
            if ($subcontrato->retencion > 0) {
                $subcontrato->generaFondoGarantia();
            }
        });
    }

    public function fondo_garantia()
    {
        return $this->hasOne(FondoGarantia::class, 'id_subcontrato');
    }

    public function generaFondoGarantia()
    {
        if ($this->retencion > 0) {
            $fondo_garantia = new FondoGarantia();
            $fondo_garantia->id_subcontrato = $this->id_transaccion;
            $fondo_garantia->save();
        } else {
            throw New \Exception('El subcontrato no tiene retención de fondo de garantía');
        }
    }
}