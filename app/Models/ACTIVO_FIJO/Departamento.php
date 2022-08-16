<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Departamento extends Model
{
    protected $connection = 'sci';
    protected $table = 'departamentos';
    public $primaryKey = 'iddepartamento';
    protected $fillable = [
    ];
}