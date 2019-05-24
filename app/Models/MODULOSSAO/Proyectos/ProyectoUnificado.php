<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:34 PM
 */

namespace App\Models\MODULOSSAO\Proyectos;


use App\Models\MODULOSSAO\BaseDatos\BaseDato;
use Illuminate\Database\Eloquent\Model;

class ProyectoUnificado extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Proyectos.ProyectosUnificados';
    protected $primaryKey = 'IDProyectoUnificado';
    public $timestamps = false;

    public function proyectos(){
        return $this->hasMany(Proyecto::class, 'IDProyecto', 'IDProyecto');
    }

    public function base(){
        return $this->hasMany(BaseDato::class, 'IDBaseDatos', 'IDBaseDatos');
    }
}