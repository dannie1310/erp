<?php

namespace App\Models\SEGURIDAD_ERP\Finanzas;

use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\IGH\Usuario;
use App\Scopes\EstatusMayorCeroScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAutorizacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.solicitud_pago_autorizacion';

    public $timestamps = false;
    protected $fillable = [
        'base_datos',
        'proyecto',
        'id_transaccion',
        'id_solicitud_autorizacion',
        'opciones',
        'numero_folio',
        'fecha',
        'fecha_registro',
        'razon_social',
        'rfc',
        'observaciones',
        'monto',
        'moneda',
        'usuario_autorizo',
        'fecha_hora_autorizacion',
        'usuario_rechazo',
        'fecha_hora_rechazo',
        'usuario_registro',
        'estado'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusMayorCeroScope);
    }

    /**
     * Relaciones
     */

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function usuarioAutorizo(){
        return $this->belongsTo(Usuario::class, 'usuario_autorizo', 'idusuario');
    }

    public function usuarioRechazo(){
        return $this->belongsTo(Usuario::class, 'usuario_rechazo', 'idusuario');
    }

    /**
     * Scopes
     */

    public function scopeAutorizacionPendiente($query)
    {
        return $query->whereNull("usuario_autorizo")
            ->whereNull("usuario_rechazo");
    }

    public function scopeRegistrada($query)
    {
        return $query->where("estatus","=",0);
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

    public function getFechaHoraAutorizacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_autorizacion);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaHoraRechazoFormatAttribute()
    {
        $date = date_create($this->fecha_hora_rechazo);
        return date_format($date,"d/m/Y H:i");
    }

    public function getRegistroAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    public function getAutorizoAttribute()
    {
        return $this->usuarioAutorizo->nombre_completo;
    }

    public function getRechazoAttribute()
    {
        return $this->usuarioRechazo->nombre_completo;
    }

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

    public function autorizar(){

        if($this->estatus == 1){
            throw New \Exception('La solicitud ya fue autorizada por '.$this->usuarioAutorizo->nombre_completo." [".$this->fecha_hora_autorizacion_format ."]");
        }
        else if($this->estatus == 2){
            throw New \Exception('La solicitud ya fue rechazada por '.$this->usuarioRechazo->nombre_completo." [".$this->fecha_hora_rechazo_format ."]");
        }
        else if($this->estatus != 0){
            throw New \Exception('La solicitud no puede ser autorizada, porque no tiene el estatus correcto.');
        }
        $this->estatus = 1;
        $this->fecha_hora_autorizacion = date('Y-m-d H:i:s');
        $this->usuario_autorizo = auth()->id();
        $this->save();

        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);

        $solicitud = \App\Models\CADECO\Finanzas\SolicitudPagoAutorizacion::where("id_transaccion","=",$this->id_transaccion)->first();
        $solicitud->fecha_hora_autorizacion = date('Y-m-d H:i:s');
        $solicitud->usuario_autorizo = auth()->id();
        $solicitud->estatus = 1;
        $solicitud->save();
        return $this;
    }

    public function rechazar($motivo){
        if($this->estatus == 1){
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

        return $this;
    }
}
