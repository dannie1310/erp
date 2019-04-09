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
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($liberacion)
        {
            $subcontrato = Subcontrato::find($liberacion->id_antecedente);
            $liberacion->tipo_transaccion = 53;
            $liberacion->opciones = 0;
            $liberacion->estado = 1;
            $liberacion->id_empresa = $subcontrato->id_empresa;
            $liberacion->id_moneda = $subcontrato->id_moneda;
            $liberacion->saldo = $liberacion->monto;
        });

        self::updating(function ($liberacion)
        {
            if($liberacion->saldo != $liberacion->monto){
                throw new \Exception('La transacción de liberación no puede ser cancelada, su saldo ya ha sido afectado');
            }
        });
    }
}