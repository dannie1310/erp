<?php

namespace App\Models\SEGURIDAD_ERP\Finanzas;

use App\Events\AutorizacionPagoAnticipado;
use App\Events\RechazoPagoAnticipado;
use App\Events\SolicitudAutorizacionPagoAnticipado;
use App\Events\SolicitudAutorizacionPagoAnticipadoSinContexto;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\AutorizacionRequerida;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\NivelAutorizacion;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Transaccion;
use App\Scopes\EstadoMayorCeroScope;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAutorizacion extends Transaccion
{

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstadoMayorCeroScope);
    }

    /**
     * Relaciones
     */

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function autorizacionesRequeridas(){
        return $this->hasMany(AutorizacionRequerida::class, "id_transaccion_general", "id");
    }

    public function autorizacionesRequeridasPendientes(){
        if(auth()->user()->firmante)
        {
            $nivel_autorizacion = NivelAutorizacion::find(auth()->user()->firmante->id_nivel_autorizacion);
            return $this->hasMany(AutorizacionRequerida::class, "id_transaccion_general", "id")
                ->whereNull("id_firmante")
                ->where("nivel_requerido","=", $nivel_autorizacion->nivel);
        }
    }

    /**
     * Scopes
     */

    public function scopeAutorizacionPendiente($query)
    {
        return $query->whereHas("autorizacionesRequeridasPendientes");
    }

    public function scopeRegistrada($query)
    {
        return $query->where("estado","=",0);
    }

    /**
     * Atributos
     */

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getMontoFormatAttribute()
    {
        return '$' . number_format(($this->monto),2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i");
    }

    public function getRegistroAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    /*public function getAutorizoAttribute()
    {
        return $this->usuarioAutorizo->nombre_completo;
    }

    public function getRechazoAttribute()
    {
        return $this->usuarioRechazo->nombre_completo;
    }*/

    public function getTipoTxtAttribute()
    {

        switch ($this->opciones){
            case 327681:
                return "Pagos a Cuenta";
                break;
            case 1:
                return "Reposición de Fondo";
                break;
            case 131073:
                return "Anticipos y Destajos";
                break;
            case 65537:
                return "Listas de Raya";
                break;
        }

    }

    public function getSolicitudPagoAnticipadoAttribute()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        $solicitud = SolicitudPagoAnticipado::withoutGlobalScopes()->where("id_transaccion","=",$this->id_transaccion)
            ->first();
        return $solicitud;
    }
    /**
     * Métodos
     */

    public function autorizar($observaciones = null){
        if(auth()->user()->firmante)
        {
            $nivel_autorizacion = NivelAutorizacion::find(auth()->user()->firmante->id_nivel_autorizacion);

            $autorizacion_pendiente = AutorizacionRequerida::where("nivel_requerido", "=", $nivel_autorizacion->nivel)
                ->whereNull("id_firmante")
                ->where("estado","=",0)
                ->where("id_transaccion_general","=",$this->id)
                ->first();


            if($autorizacion_pendiente)
            {
                $autorizacion_pendiente->id_firmante = auth()->user()->firmante->id;
                $autorizacion_pendiente->id_usuario_autorizo = auth()->id();
                $autorizacion_pendiente->fecha_hora_autorizacion = date('Y-m-d H:i:s');
                $autorizacion_pendiente->observaciones = $observaciones;
                $autorizacion_pendiente->estado = 1;
                $autorizacion_pendiente->save();

                DB::purge('cadeco');
                Config::set('database.connections.cadeco.database', $this->base_datos);

                $autorizacion_pendiente_sao = \App\Models\CADECO\EsquemaAutorizacion\AutorizacionRequerida::find($autorizacion_pendiente->id_autorizacion_requerida);
                if($autorizacion_pendiente_sao)
                {
                    $autorizacion_pendiente_sao->id_firmante = auth()->user()->firmante->id;
                    $autorizacion_pendiente_sao->id_usuario_autorizo = auth()->id();
                    $autorizacion_pendiente_sao->fecha_hora_autorizacion = date('Y-m-d H:i:s');
                    $autorizacion_pendiente_sao->observaciones = $observaciones;
                    $autorizacion_pendiente_sao->estado = 1;
                    $autorizacion_pendiente_sao->save();
                }else{
                    throw new \Exception('La solicitud no tiene correspondencia con un registro en el SAO');
                }

                $autorizaciones_pendientes = AutorizacionRequerida::whereNull("id_firmante")
                    ->where("estado","=",0)
                    ->get();

                if(count($autorizaciones_pendientes) == 0)
                {
                    $this->estado = 2;
                    $this->save();
                    event(new AutorizacionPagoAnticipado($this, $this->solicitud_pago_anticipado));
                }else{
                    $this->estado = 1;
                    $this->save();
                    event(new SolicitudAutorizacionPagoAnticipadoSinContexto($this));
                }

            }
            else
            {
                $ultima_autorizacion = AutorizacionRequerida::where("nivel_requerido", "=", $nivel_autorizacion->nivel)
                    ->whereNotNull("id_firmante")
                    ->where("id_transaccion_general","=",$this->id)
                    ->first();
                if ($ultima_autorizacion && $ultima_autorizacion->id_usuario_autorizo) {
                    throw new \Exception('La solicitud ya fue autorizada por ' . ucwords(strtolower($ultima_autorizacion->usuarioAutorizo->nombre_completo)) . " el " . $ultima_autorizacion->fecha_hora_autorizacion_format . "");
                }
                if ($ultima_autorizacion && $ultima_autorizacion->id_usuario_rechazo) {
                    throw new \Exception('La solicitud ya fue rechazada por ' . ucwords(strtolower($ultima_autorizacion->usuarioRechazo->nombre_completo)) . " el " . $ultima_autorizacion->fecha_hora_rechazo_format . " por el siguiente motivo: ". $ultima_autorizacion->observaciones);
                }
            }

            return $this;
        }
        else{
            throw New \Exception("No tiene asignado un nivel de autorización, por favor envie un correo a la dirección \n soporte_aplicaciones@desarrollo-hi.atlassian.net");
        }
    }

    public function rechazar($motivo){

        if(auth()->user()->firmante)
        {
            $nivel_autorizacion = NivelAutorizacion::find(auth()->user()->firmante->id_nivel_autorizacion);

            $autorizacion_pendiente = AutorizacionRequerida::where("nivel_requerido", "=", $nivel_autorizacion->nivel)
                ->whereNull("id_firmante")
                ->where("estado","=",0)
                ->where("id_transaccion_general","=",$this->id)
                ->first();


            if($autorizacion_pendiente)
            {
                $autorizacion_pendiente->id_firmante = auth()->user()->firmante->id;
                $autorizacion_pendiente->id_usuario_rechazo = auth()->id();
                $autorizacion_pendiente->fecha_hora_rechazo = date('Y-m-d H:i:s');
                $autorizacion_pendiente->observaciones = $motivo;
                $autorizacion_pendiente->estado = 1;
                $autorizacion_pendiente->save();

                DB::purge('cadeco');
                Config::set('database.connections.cadeco.database', $this->base_datos);

                $autorizacion_pendiente_sao = \App\Models\CADECO\EsquemaAutorizacion\AutorizacionRequerida::find($autorizacion_pendiente->id_autorizacion_requerida);
                if($autorizacion_pendiente_sao)
                {
                    $autorizacion_pendiente_sao->id_firmante = auth()->user()->firmante->id;
                    $autorizacion_pendiente_sao->id_usuario_rechazo = auth()->id();
                    $autorizacion_pendiente_sao->fecha_hora_rechazo = date('Y-m-d H:i:s');
                    $autorizacion_pendiente_sao->observaciones = $motivo;
                    $autorizacion_pendiente_sao->estado = 1;
                    $autorizacion_pendiente_sao->save();
                }else{
                    throw new \Exception('La solicitud no tiene correspondencia con un registro en el SAO');
                }

            }
            else
            {
                $ultima_autorizacion = AutorizacionRequerida::where("nivel_requerido", "=", $nivel_autorizacion->nivel)
                    ->whereNotNull("id_firmante")
                    ->where("id_transaccion_general","=",$this->id)
                    ->first();
                if ($ultima_autorizacion && $ultima_autorizacion->id_usuario_autorizo) {
                    throw new \Exception('La solicitud ya fue autorizada por ' .ucwords(strtolower( $ultima_autorizacion->usuarioAutorizo->nombre_completo)) . " el " . $ultima_autorizacion->fecha_hora_autorizacion_format . "");
                }
                if ($ultima_autorizacion && $ultima_autorizacion->id_usuario_rechazo) {
                    throw new \Exception('La solicitud ya fue rechazada por ' . ucwords(strtolower($ultima_autorizacion->usuarioRechazo->nombre_completo)) . " el " . $ultima_autorizacion->fecha_hora_rechazo_format . " por el siguiente motivo: ". $ultima_autorizacion->observaciones);
                }
            }
            $this->estado = -2;
            $this->save();
            event(new RechazoPagoAnticipado($this, $this->solicitud_pago_anticipado));
            return $this;
        }
        else{
            throw New \Exception("No tiene asignado un nivel de autorización, por favor envie un correo a la dirección \n soporte_aplicaciones@desarrollo-hi.atlassian.net");
        }

        /*if($this->estatus == 1){
            throw New \Exception('La solicitud ya fue autorizada por '.$this->usuarioAutorizo->nombre_completo." [".$this->fecha_hora_autorizacion_format ."]");
        }
        else if($this->estatus == 2){
            throw New \Exception('La solicitud ya fue rechazada por '.$this->usuarioRechazo->nombre_completo." [".$this->fecha_hora_rechazo_format ."]");
        }
        else if($this->estatus != 0){
            throw New \Exception('La solicitud no puede ser autorizada, porque no tiene el estatus correcto.');
        }
        $this->motivo = $motivo;
        $this->usuario_rechazo = auth()->id();
        $this->fecha_hora_rechazo = date('Y-m-d H:i:s');
        $this->estatus = 2;
        $this->save();

        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);

        $solicitud = \App\Models\CADECO\Finanzas\SolicitudPagoAutorizacion::where("id_transaccion","=",$this->id_transaccion)->first();
        $solicitud->motivo = $motivo;
        $solicitud->estatus = 2;
        $solicitud->usuario_rechazo = auth()->id();
        $solicitud->fecha_hora_rechazo = date('Y-m-d H:i:s');
        $solicitud->save();
        event(new RechazoPagoAnticipado($this, $this->solicitud_pago_anticipado));
        return $this;*/
    }
}
