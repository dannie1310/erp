<?php

namespace App\Models\CADECO;

use App\Models\CADECO\ItemAplicacionManual;
class AplicacionManual extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 70;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 70);
        });
    }

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'fecha',
        'id_obra',
        'monto',
        'referencia',
        'tipo_transaccion',
        'tipo_cambio',
        "id_empresa",
        "id_moneda",
        "id_usuario",
        "saldo",
        "impuesto",
        "observaciones",
        "estado",
        "opciones",
        "numero_folio",
    ];

    public function partidas(){
        return $this->hasMany(ItemAplicacionManual::class, 'id_transaccion', 'id_transaccion');
    }
}
