<?php


namespace App\Models\CADECO\SubcontratosEstimaciones;

use Illuminate\Database\Eloquent\Model;

class PenalizacionLiberacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.penalizacion_liberacion';
    protected $primaryKey = 'id_liberacion';
    public $timestamps = false;
}