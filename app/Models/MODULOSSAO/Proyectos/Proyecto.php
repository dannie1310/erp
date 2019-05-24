<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:33 PM
 */

namespace App\Models\MODULOSSAO\Proyectos;


use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Proyectos.Proyectos';
    protected $primaryKey = 'IDProyecto';
    public $timestamps = false;
}