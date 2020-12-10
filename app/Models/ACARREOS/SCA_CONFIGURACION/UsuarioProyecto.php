<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use App\Models\ACARREOS\ConfiguracionDiaria;
use App\Models\ACARREOS\Telefono;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class UsuarioProyecto extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.usuarios_proyectos';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    /**
     * Relaciones Eloquent
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_intranet', 'idusuario');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    public function roles()
    {
        return $this->hasMany(RolUsuario::class, 'user_id', 'id_usuario_intranet');
    }

    public function configuracionDiaria()
    {
        return $this->belongsTo(ConfiguracionDiaria::class, 'id_usuario_intranet', 'id_usuario');
    }

    public function telefono()
    {
        return $this->belongsTo(Telefono::class, 'id_usuario_intranet', 'id_checador');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus',  1);
    }

    public function scopeOrdenarProyectos($query)
    {
        return $query->orderBy('id_proyecto',  'desc');
    }

    public function scopeEsChecador($query)
    {
        return $query->whereHas('roles', function ($q){
            return $q->esChecador();
        });
    }

    public function scopeTelefonoAsignado($query, $imei)
    {
        return $query->whereHas('telefono', function ($q) use ($imei){
            return $q->where('imei', '=', $imei);
        });
    }


    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
