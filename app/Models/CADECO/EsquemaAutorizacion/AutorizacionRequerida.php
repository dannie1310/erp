<?php

namespace App\Models\CADECO\EsquemaAutorizacion;

use Illuminate\Database\Eloquent\Model;

class AutorizacionRequerida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'EsquemaAutorizacion.autorizaciones_requeridas';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_nivel_requerido',
        'nivel_requerido',
        'id_firmante',
        'id_usuario_autorizo',
        'id_usuario_rechazo',
        'observaciones',
        'fecha_hora_autorizacion',
        'fecha_hora_rechazo',
        'estado',
        'usuario_registro'
    ];

    public function usuarioAutorizo(){
        if($this->fecha_hora_firma){
            return $this->belongsTo(Usuario::class, 'id_firmante', 'idusuario');
        }
    }

    public function usuarioRechazo(){
        if($this->fecha_hora_rechazo){
            return $this->belongsTo(Usuario::class, 'id_firmante', 'idusuario');
        }
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
