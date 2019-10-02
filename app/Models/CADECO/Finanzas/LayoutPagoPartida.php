<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/09/2019
 * Time: 03:52 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Factura;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use Illuminate\Database\Eloquent\Model;

class LayoutPagoPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.layout_pagos_partidas';
    public $timestamps = false;

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_transaccion', 'id_transaccion');
    }

    public function solicitudPagoAnticipado()
    {
        return $this->belongsTo(SolicitudPagoAnticipado::class, 'id_transaccion', 'id_transaccion');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class,'id_transaccion_pago', 'id_transaccion');
    }

    public function pagoVario()
    {
        return $this->belongsTo(PagoVario::class,'id_transaccion_pago', 'id_transaccion');
    }

    public function pagoACuenta()
    {
        return $this->belongsTo(PagoACuenta::class,'id_transaccion_pago', 'id_transaccion');
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento_remesa', 'IDDocumento');
    }
    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }
}