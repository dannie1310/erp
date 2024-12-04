<?php

namespace App\Models\MODULOSSAO\InterfazNominas;

use Illuminate\Database\Eloquent\Model;

class ProyectoIFS extends Model
{
    protected $connection = 'interfaz_nominas';
    protected $table = 'InterfazNominas.ProyectoIFS';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
