<?php


namespace App\Models\MODULOSSAO\Seguridad;


use App\Facades\Context;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class PerfilUsuarioAplicacion extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Seguridad.PerfilesUsuariosAplicacion';
    protected $primaryKey = 'IDUsuario';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
