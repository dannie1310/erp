<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 09/02/2019
 * Time: 02:36 PM
 */

namespace App\Models\CADECO;


class LiberacionFondoGarantia extends Transaccion
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
}