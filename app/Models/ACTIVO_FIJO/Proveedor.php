<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Proveedor extends Model
{
    protected $connection = 'sci';
    protected $table = 'proveedores';
    public $primaryKey = 'idProveedor';
    protected $fillable = [
    ];
}