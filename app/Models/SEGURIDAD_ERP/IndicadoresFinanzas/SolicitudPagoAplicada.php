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
        'folio_solicitud',
        'monto',
        'fecha_autorizacion_remesa',
        'monto_autorizado_remesa',
        'fecha_pago',
        'monto_pagado',
        'fecha_aplicacion_manual',
        'monto_aplicado',
        'pendiente',
        'estado_registro'
    ];

    //relaciones
    //scope
    public function scopePendientes($query)
    {
        return $query->where("pendiente",">=","0.99");
    }
    //atributos
    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getMontoFormatAttribute()
    {
        return '$' . number_format(($this->monto),2);
    }

    public function getMontoAplicadoFormatAttribute()
    {
        return '$' . number_format(($this->monto_aplicado),2);
    }

    public function getMontoPagadoFormatAttribute()
    {
        return '$' . number_format(($this->monto_pagado),2);
    }

    public function getPendienteFormatAttribute()
    {
        return '$' . number_format($this->pendiente,2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaSolicitudFormatAttribute()
    {
        $date = date_create($this->fecha_solicitud);
        return date_format($date,"d/m/Y");
    }
    //metodos

}
