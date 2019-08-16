<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Moneda;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Cuenta;

class Pago extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'numero_folio',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        "id_cuenta",
        "id_empresa",
        "id_moneda",
        "saldo",
        "destino"
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
        self::creating(function ($model) {
            $model->tipo_transaccion = 82;
            $model->opciones = 0;
            $model->fecha = date('Y-m-d');
            $model->cumplimiento =  date('Y-m-d');
            $model->vencimiento = date('Y-m-d');
        });
    }
    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function cuenta(){
        return $this->hasOne(Cuenta::class, 'id_cuenta', 'id_cuenta');
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }
}
