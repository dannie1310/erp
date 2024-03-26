<?php


namespace App\Models\MODULOSSAO\Seguridad;


use App\Facades\Context;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class UsuarioAplicacion extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Seguridad.UsuariosAplicaciones';
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
