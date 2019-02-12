<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 09/02/2019
 * Time: 02:36 PM
 */

namespace App\Models\CADECO;


class DescuentoFondoGarantia extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'monto',
        'referencia',
        'comentario',
        'observaciones',
    ];

    public $usuario_registra = 666;
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($descuento) {
            $subcontrato = Subcontrato::find($descuento->id_antecedente);
            $descuento->tipo_transaccion = 53;
            $descuento->opciones = 1;
            $descuento->estado = 1;
            $descuento->id_empresa = $subcontrato->id_empresa;
            $descuento->id_moneda = $subcontrato->id_moneda;
        });
    }
}