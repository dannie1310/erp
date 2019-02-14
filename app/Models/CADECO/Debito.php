<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 31/01/19
 * Time: 04:58 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;

class Debito extends Transaccion
{
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
            return $query->where('tipo_transaccion', '=', 84);
        });

        self::creating(function ($model) {
            $model->estado = 1;
            $model->id_moneda = Obra::query()->find(Context::getIdObra())->id_moneda;
            $model->opciones = 1;
            $model->tipo_transaccion = 84;
            $model->vencimiento = $model->cumplimiento;
        });

        self::updating(function ($model) {
            $model->vencimiento = $model->cumplimiento;
        });
    }
}