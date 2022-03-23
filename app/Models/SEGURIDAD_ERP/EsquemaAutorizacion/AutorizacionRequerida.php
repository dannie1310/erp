<?php

namespace App\Models\SEGURIDAD_ERP\EsquemaAutorizacion;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class AutorizacionRequerida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'EsquemaAutorizacion.autorizaciones_requeridas';
    public $timestamps = false;

    protected $fillable = [
        'base_datos',
        'id_transaccion_general',
        'id_transaccion_requerida',
        'id_autorizacion_requerida',
        'id_transaccion',
        'id_nivel_requerido',
        'nivel_requerido',
        'id_firmante',
        'id_usuario_autorizo',
        'id_usuario_rechazo',
        'observaciones',
        'usuario_registro',
        'fecha_hora_autorizacion',
        'fecha_hora_rechazo',
        'estado',
    ];


    public function usuarioAutorizo(){
        return $this->belongsTo(Usuario::class, 'id_usuario_autorizo', 'idusuario');
    }

    public function usuarioRechazo(){
        return $this->belongsTo(Usuario::class, 'id_usuario_rechazo', 'idusuario');
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

    public function scopeRegistrada($query)
    {
        return $query->where("estado","=",0);
    }
}
