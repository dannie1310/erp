<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 31/01/19
 * Time: 04:58 PM
 */

namespace App\Models\CADECO;



class Debito extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;
    public const NOMBRE = "Débito";

    protected $fillable = [
        'cumplimiento',
        'fecha',
        'id_cuenta',
        'impuesto',
        'monto',
        'referencia',
        'observaciones',
        'id_usuario'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 84);
        });
    }
}
