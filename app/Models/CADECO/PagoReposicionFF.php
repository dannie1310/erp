<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:12 AM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Finanzas\PagoEliminadoLog;

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

    public function elimina()
    {
        /**
         * Se cambia el valor del saldo del fondo
         */
        $saldo_a_modificar = $this->fondo->saldo + $this->monto;
        $consulta = "'Pago reposicionff : id_fondo = ".$this->fondo->id_fondo." saldo = ".$this->fondo->saldo." monto= ".$this->monto." saldo(cambio)= ".$saldo_a_modificar."'";
        $this->fondo->update(['saldo' => $saldo_a_modificar]);
        PagoEliminadoLog::create([
            'id_transaccion' => $this->id_transaccion,
            'consulta' => $consulta
        ]);
        return $this->delete();
    }
}
