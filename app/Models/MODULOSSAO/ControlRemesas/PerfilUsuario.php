<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Facades\Context;
use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class PerfilUsuario extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.PerfilesUsuarios';
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
