<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class GrlProyecto extends Model
{
    protected $connection = 'repseg';
    protected $table = 'grl_proyecto';
    protected $primaryKey = 'idproyecto';
    public $timestamps = false;
}
