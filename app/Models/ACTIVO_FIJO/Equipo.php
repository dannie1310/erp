<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equipo extends Model
{
    protected $connection = 'sci';
    protected $table = 'equipos';
    public $primaryKey = 'idEquipo';
    protected $fillable = [
    ];
}