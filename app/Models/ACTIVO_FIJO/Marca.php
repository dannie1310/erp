<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Marca extends Model
{
    protected $connection = 'sci';
    protected $table = 'marcas';
    public $primaryKey = 'idMarca';
    protected $fillable = [
    ];
}