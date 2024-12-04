<?php

namespace App\Models\MODULOSSAO\InterfazNominas;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $connection = 'interfaz_nominas';
    protected $table = 'InterfazNominas.Parametros';
    public $timestamps = false;
}
