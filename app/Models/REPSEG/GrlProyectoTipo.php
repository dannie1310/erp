<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class GrlProyectoTipo extends Model
{
    protected $connection = 'repseg';
    protected $table = 'grl_proyecto_tipo';
    protected $primaryKey = 'idproyecto_tipo';
    public $timestamps = false;
}
