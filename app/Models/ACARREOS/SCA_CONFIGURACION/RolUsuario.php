<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.role_user';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('proyectos');
        });
    }

    /**
     * Relaciones Eloquent
     */
    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'id_proyecto', 'id_proyecto');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,'user_id','idusuario');
    }

    /**
     * Scopes
     */
    public function scopeChecador($query)
    {
        return $query->where('role_id', 7);
    }

    /**
     * Attributes
     */



    /**
     * Métodos
     */
}
