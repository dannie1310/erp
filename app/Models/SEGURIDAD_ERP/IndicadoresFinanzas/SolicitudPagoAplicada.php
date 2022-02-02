<?php

namespace App\Models\SEGURIDAD_ERP\IndicadoresFinanzas;


use App\Models\CADECO\Banco;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAplicada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'IndicadoresFinanzas.solicitudes_pago_aplicadas';
    public $timestamps = false;
    protected $fillable =[
        'base_datos',
        'id_obra',
        'nombre_obra',
        'id_transaccion',
        'fecha_solicitud',
        'monto',
        'fecha_autorizacion_remesa',
        'monto_autorizado_remesa',
        'fecha_pago',
        'monto_pagado',
        'fecha_aplicacion_manual',
        'monto_aplicado',
        'estado_registro'
    ];

}
