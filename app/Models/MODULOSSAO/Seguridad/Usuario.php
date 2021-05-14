<?php


namespace App\Models\MODULOSSAO\Seguridad;


use App\Facades\Context;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Seguridad.Usuarios';
    protected $primaryKey = 'IDUsuario';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function usuarioProyectos()
    {
        return $this->hasMany(UsuarioProyecto::class, 'IDUsuario', 'IDUsuario');
    }

    /**
     * Scopes
     */
    public function scopeGetUsuario($query)
    {
        return $query->where('Usuario', '=', auth()->user()->usuario)->where('Inactivo', 0);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
