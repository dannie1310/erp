<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinDimIngresoCliente extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_dim_ingreso_clientes';
    protected $primaryKey = 'idcliente';
    public $timestamps = false;
}
