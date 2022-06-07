<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class FinDimIngresoPartida extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_dim_ingreso_partidas';
    protected $primaryKey = 'idpartida';
    public $timestamps = false;
}
