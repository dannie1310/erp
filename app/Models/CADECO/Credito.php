<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 31/01/19
 * Time: 04:47 PM
 */

namespace App\Models\CADECO;


class Credito extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;

    protected $fillable = [
        'cumplimiento',
        'fecha',
        'id_cuenta',
        'impuesto',
        'monto',
        'referencia',
        'observaciones',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
              return $query->where('tipo_transaccion', '=', 83);
        });
    }
}