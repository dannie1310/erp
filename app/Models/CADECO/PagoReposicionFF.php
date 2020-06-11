<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:12 AM
 */

namespace App\Models\CADECO;

class PagoReposicionFF extends Pago
{
    public const TIPO_ANTECEDENTE = 72;
    public const OPCION_ANTECEDENTE = 1;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'tipo_transaccion',
        'fecha',
        'estado',
        'id_obra',
        'id_cuenta',
        "id_moneda",
        'cumplimiento',
        'vencimiento',
        "opciones",
        'monto',
        "saldo",
        'referencia',
        "destino",
        'observaciones',
        'tipo_cambio',
        "id_usuario"
    ];
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
    }

    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudReposicionFF::class, 'id_antecedente', 'id_transaccion');
    }

    public function eliminar($motivo)
    {
        $this->fondo->update(['saldo' => $this->fondo->saldo + $this->monto]);

    }
}
