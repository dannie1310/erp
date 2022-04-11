<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\EsquemaAutorizacion\AutorizacionRequerida;
use App\Models\CADECO\Finanzas\SolicitudPagoAutorizacion;
use Illuminate\Support\Facades\DB;

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
        return $this->HasOne(\App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion::class,'id_transaccion','id_transaccion')
            ->whereIn("estado",[0,1,2])
            ->where("base_datos","=",Context::getDatabase());
    }

    public function solicitudPagoAutorizacionGeneral()
    {
        return $this->HasOne(\App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion::class,'id_transaccion','id_transaccion')
            ->where("base_datos","=",Context::getDatabase());
    }

    public function transaccionGeneral()
    {
        return $this->HasOne(\App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Transaccion::class,'id_transaccion','id_transaccion')
            ->where("base_datos","=",Context::getDatabase())
            ->where("estado",">=",0);
    }

    public function autorizacionesRequeridas()
    {
        return $this->HasMany(AutorizacionRequerida::class,'id_transaccion','id_transaccion')
            ->where("estado",">=",0);
    }

    public function autorizacionesRequeridasGenerales()
    {
        return $this->HasMany(\App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\AutorizacionRequerida::class,'id_transaccion','id_transaccion')
            ->where("base_datos","=",Context::getDatabase())
            ->where("estado",">=",0);
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

        if($this->solicitudPagoAutorizacionActiva){
            throw New \Exception("Ya existe una solicitud de autorización para este pago anticipado.");
        }

        DB::connection('cadeco')->beginTransaction();
        DB::connection('seguridad')->beginTransaction();

        try {

            $this->transaccionGeneral()->registrada()->update(["estado"=>-1]);
            $this->autorizacionesRequeridasGenerales()->registrada()->update(["estado"=>-1]);
            $this->autorizacionesRequeridas()->registrada()->update(["estado"=>-1]);

            $proyecto = Obra::find(Context::getIdObra());

            $transaccion_general = \App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Transaccion::create([
                "id_transaccion"=>$this->id_transaccion,
                "id_tipo_transaccion"=>1,
                "base_datos"=>Context::getDatabase(),
                'proyecto'=>$proyecto->nombre,
                "opciones"=>$this->opciones,
                'numero_folio'=>$this->numero_folio,
                'fecha'=>$this->fecha,
                'fecha_registro'=>$this->FechaHoraRegistro,
                'razon_social'=>$this->razon_social,
                'rfc'=>$this->rfc,
                'observaciones'=>$this->observaciones,
                'monto'=>$this->monto,
                'moneda'=>$this->moneda->abreviatura,
                "usuario_registro"=>auth()->id(),
                "hash"=>'-'
            ]);

            $autorizacion_1814 = AutorizacionRequerida::create([
                "id_transaccion"=>$this->id_transaccion,
                "id_nivel_requerido"=>2,
                "nivel_requerido"=>4,
                "usuario_registro"=>auth()->id(),
            ]);

            \App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\AutorizacionRequerida::create(
                [
                    "base_datos"=>Context::getDatabase(),
                    "id_transaccion_general" => $transaccion_general->id,
                    "id_transaccion" => $this->id_transaccion,
                    "id_autorizacion_requerida" => $autorizacion_1814->id,
                    "id_nivel_requerido"=>2,
                    "nivel_requerido"=>4,
                    "usuario_registro"=>auth()->id(),
                ]

            );

            if($this->monto_pesos >= 50000)
            {
                $autorizacion_1814_2 = AutorizacionRequerida::create([
                    "id_transaccion"=>$this->id_transaccion,
                    "id_nivel_requerido"=>1,
                    "nivel_requerido"=>2,
                    "usuario_registro"=>auth()->id(),
                ]);

                \App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\AutorizacionRequerida::create(
                    [
                        "base_datos"=>Context::getDatabase(),
                        "id_transaccion_general" => $transaccion_general->id,
                        "id_transaccion" => $this->id_transaccion,
                        "id_autorizacion_requerida" => $autorizacion_1814_2->id,
                        "id_nivel_requerido"=>1,
                        "nivel_requerido"=>2,
                        "usuario_registro"=>auth()->id(),
                    ]
                );
            }

        }catch (\Exception $e)
        {
            DB::connection('seguridad')->rollBack();
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage()/*."-".$e->getFile()."-".$e->getLine()*/);
        }

        DB::connection('seguridad')->commit();
        DB::connection('cadeco')->commit();

        return $this;
    }

    public function solicitarAutorizacion1()
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
