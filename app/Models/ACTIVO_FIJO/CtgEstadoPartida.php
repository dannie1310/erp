<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CtgEstadoPartida extends Model
{
    protected $connection = 'sci';
    protected $table = 'cat_estado_partidas';
    public $primaryKey = 'idEstado';
    protected $fillable = [
    ];
}