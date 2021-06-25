<?php


namespace App\Models\MODULOSSAO\Proyectos;


use Illuminate\Database\Eloquent\Model;

class TipoProyecto extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Proyectos.TiposProyecto';
    protected $primaryKey = 'IDTipoProyecto';
    public $timestamps = false;

}
