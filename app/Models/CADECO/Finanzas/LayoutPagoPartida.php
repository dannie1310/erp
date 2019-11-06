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
use App\Models\CADECO\PagoFactura;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\Transaccion;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Finanzas\DocumentoPagable;

class LayoutPagoPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.layout_pagos_partidas';
    public $timestamps = false;

    protected $fillable = [
        'id_layout_pagos',
        'id_transaccion',
        'id_moneda_transaccion',
        'monto_transaccion',
        'saldo_transaccion',
        'tipo_cambio',
        'id_cuenta_cargo',
        'id_moneda_cuenta_cargo',
        'cuenta_cargo',
        'fecha_pago',
        'monto_pagado',
        'referencia_pago',
        'id_documento_remesa',
        'id_transaccion_pago',
        'monto_autorizado_remesa',
    ];

    public function layoutPago()
    {
        return $this->belongsTo(LayoutPago::class, 'id_layout_pagos', 'id');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_transaccion', 'id_transaccion');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_transaccion', 'id_transaccion');
    }

    public function documento_pagable(){
        return $this->belongsTo(DocumentoPagable::class, 'id_transaccion', 'id_transaccion');
    }

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class,'id_transaccion_pago', 'id_transaccion');
    }

    public function pagoVario()
    {
        return $this->belongsTo(PagoVario::class,'id_transaccion_pago', 'id_transaccion');
    }

    public function pagoFactura()
    {
        return $this->belongsTo(PagoFactura::class,'id_transaccion_pago', 'id_transaccion');
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
        return $this->belongsTo(Moneda::class, 'id_moneda_transaccion', 'id_moneda');
    }

    public function cuenta(){
        return $this->hasOne(Cuenta::class, 'id_cuenta', 'id_cuenta_cargo');
    }

    public function validarRegistro()
    {
        if($this->id_cuenta_cargo == null && $this->id_transaccion_pago == NULL){
            abort(403, 'No selecciono la cuenta cargo.');
        }

        if($this->monto_pagado == 0){
            abort(403, 'El monto pagado no debe ser cero.');
        }

        if($this->tipo_cambio == 0){
            abort(403, 'El tipo de cambio no puede ser cero.');
        }
    }

    public function getTipoCambioFormatAttribute(){
        if($this->tipo_cambio > 1){
            return number_format($this->tipo_cambio,4);
        }else{
            return 1;
        }

    }

    public function getMontoPagadoDocumentoAttribute(){
        $moneda_obra = $this->transaccion->obra->moneda;
        if($moneda_obra->id_moneda == $this->id_moneda_cuenta_cargo){
            $monto_pagado = $this->monto_pagado / $this->tipo_cambio;
        }else{
            $monto_pagado = $this->monto_pagado * $this->tipo_cambio;
        }
        return $monto_pagado;
    }

    public function getMontoPagadoFormatAttribute(){
        return "$ " . number_format($this->monto_pagado,2,".",",");
    }

    public function getMontoPagadoDocumentoFormatAttribute(){
        return "$ " . number_format($this->monto_pagado_documento,2,".",",");
    }

    public function getFolioPagoFormatAttribute(){
        if($this->pago){
            return $this->pago->numero_folio_format;
        }else{
            return '-';
        }
    }
}
