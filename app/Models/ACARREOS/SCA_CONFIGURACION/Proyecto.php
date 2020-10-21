<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'sca_configuracion.proyectos';
    public $timestamps = false;
}
