<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    protected $connection = 'sci';
    protected $table = 'empresas';
    public $primaryKey = 'idEmpresa';
    protected $fillable = [
    ];
}