<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinDimIngresoEmpresa extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_dim_ingreso_empresas';
    protected $primaryKey = 'idempresa';
    public $timestamps = false;
}
