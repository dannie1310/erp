<?php


namespace App\Models\MODULOSSAO\Proyectos;


use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Proyectos.Empresas';
    protected $primaryKey = 'IDEmpresa';
    public $timestamps = false;

}
