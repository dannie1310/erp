<?php

namespace App\Models\MODULOSSAO\InterfazNominas;

use Illuminate\Database\Eloquent\Model;

class CuentaContableIFS extends Model
{
    protected $connection = 'interfaz_nominas';
    protected $table = 'InterfazNominas.CuentasContablesIFS';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
