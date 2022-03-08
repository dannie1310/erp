<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Finanzas\SolicitudPagoAutorizacion;

class Solicitud extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72);
        });
    }

    public function pago()
    {
        return $this->HasOne(Pago::class,'id_antecedente','id_transaccion');
    }

    public function fondo()
    {
        return $this->belongsTo(Fondo::class, 'id_referente', 'id_fondo');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function pago_cuenta()
    {
        return $this->belongsTo(PagoACuenta::class, 'id_referente', 'id_referente');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function solicitudPagoAutorizacion()
    {
        return $this->HasOne(SolicitudPagoAutorizacion::class,'id_transaccion','id_transaccion');
    }

    public function solicitudPagoAutorizacionActiva()
    {
        return $this->HasOne(SolicitudPagoAutorizacion::class,'id_transaccion','id_transaccion')
            ->where("estatus","=",0);
    }

    public function solicitudPagoAutorizacionGeneral()
    {
        return $this->HasOne(\App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion::class,'id_transaccion','id_transaccion')
            ->where("base_datos","=",Context::getDatabase());
    }

    /**
     * Scopes
     */

    public function scopeAutorizacionPendiente($query)
    {
        return $query->doesnthave('solicitudPagoAutorizacion');
    }

    /**
     * Atributos
     */

    public function getRazonSocialAttribute()
    {
        if($this->empresa)
        {
            return $this->empresa->razon_social;
        }else if($this->fondo)
        {
            return $this->fondo->nombre;
        }else{
            return "";
        }
    }
    public function getRfcAttribute()
    {
        if($this->empresa)
        {
            return $this->empresa->rfc;
        }else{
            return "";
        }
    }

    /**
     * Métodos
     */

    public function generaPago($data)
    {
        $pago = null;
        if($this->opciones == 1){
            $pago = SolicitudReposicionFF::find($this->id_transaccion)->generaPago($data);
        }elseif($this->opciones == 327681){
            $pago = SolicitudPagoAnticipado::find($this->id_transaccion)->generaPago($data);
        }elseif($this->opciones == 131073){
            $pago = SolicitudAnticipoDestajo::find($this->id_transaccion)->generaPago($data);
        }elseif ($this->opciones == 65537){
            $pago = SolicitudListaRaya::find($this->id_transaccion)->generaPago($data);
        }
        return $pago;
    }

    public function scopePendientePago($query)
    {
        return $query->where('estado', '!=', 2);
    }

    public function actualizaEstadoPagada(){
        $this->estado = 2;
        $this->save();
    }

    /**
     * Regresar saldo por eliminación de pago
     */
    public function cambiarEstadoPorEliminacion()
    {
        if($this->estado == 2)
        {
            $this->estado = 0;
        }
        $this->save();
    }

    public function solicitarAutorizacion()
    {
        $this->solicitudPagoAutorizacionGeneral()->registrada()->update(["estatus"=>-1]);
        $this->solicitudPagoAutorizacion()->registrada()->update(["estatus"=>-1]);
        $solicitud_1814 = $this->solicitudPagoAutorizacion()->create([
            "usuario_registro"=>auth()->id(),
        ]);

        $proyecto = Obra::find(Context::getIdObra());

        $this->solicitudPagoAutorizacionGeneral()->create([
            "usuario_registro"=>auth()->id(),
            "opciones"=>$this->opciones,
            "base_datos"=>Context::getDatabase(),
            'proyecto'=>$proyecto->nombre,
            'numero_folio'=>$this->numero_folio,
            'fecha'=>$this->fecha,
            'fecha_registro'=>$this->FechaHoraRegistro,
            'razon_social'=>$this->razon_social,
            'rfc'=>$this->rfc,
            'observaciones'=>$this->observaciones,
            'monto'=>$this->monto,
            'moneda'=>$this->moneda->abreviatura,
            'id_solicitud_autorizacion' => $solicitud_1814->id
        ]);

        return $this;
    }
}
