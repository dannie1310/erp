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
    public const OPCION_ANTECEDENTE = 2;

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'monto',
        'referencia',
        'comentario',
        'observaciones',
        'id_usuario'
    ];

    public $usuario_registra = 666;
}
