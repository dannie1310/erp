<?php


namespace App\Models\MODULOSSAO\Seguridad;


use App\Models\MODULOSSAO\Proyectos\Proyecto;
use Illuminate\Database\Eloquent\Model;

class UsuarioProyecto extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Seguridad.UsuariosProyectos';
    public $timestamps = false;

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'IDProyecto', 'IDProyecto');
    }
}
