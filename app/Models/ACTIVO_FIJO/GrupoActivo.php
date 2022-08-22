<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GrupoActivo extends Model
{
    protected $connection = 'sci';
    protected $table = 'grupos_activo';
    public $primaryKey = 'idGrupo';
    protected $fillable = [
    ];
}