<?php


namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Transaccion;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use Illuminate\Database\Eloquent\Model;

class BitacoraSantanderPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.bitacora_santander_partidas';
    public $timestamps = false;

    protected $fillable = [
        'id_bitacora_santander',
        'id_distribucion_recursos_rem_partida',
        'id_transaccion_pago',
        'monto_pagado',
        'referencia_pago',
        'cuenta_abono',
        'id_cuenta_abono',
        'cuenta_cargo',
        'id_cuenta_cargo'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
        });

        self::creating(function ($model) {
        });
    }

    public function bitacora(){
        return $this->belongsTo(BitacoraSantander::class, 'id', 'id_bitacora_santander');
    }

    public function cuentaCargo()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta','id_cuenta_cargo');
    }

    public function cuentaAbono()
    {
        return $this->belongsTo(CuentaBancariaEmpresa::class, 'id', 'id_cuenta_abono');
    }

    public function distribucionRemesaPartida(){
        return $this->belongsTo(DistribucionRecursoRemesaPartida::class, 'id', 'id_distribucion_recursos_rem_partida');
    }

    public function documentoRemesa(){
        return $this->belongsTo(Documento::class, 'IDDocumento', 'id_documento_remesa');
    }

    public function transaccionPago(){
        return $this->belongsTo(Transaccion::class, 'id_transaccion', 'id_transaccion_pago');
    }



}
