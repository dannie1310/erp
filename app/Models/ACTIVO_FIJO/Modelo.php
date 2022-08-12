<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Modelo extends Model
{
    protected $connection = 'sci';
    protected $table = 'modelos';
    public $primaryKey = 'idModelo';
    protected $fillable = [
    ];
}