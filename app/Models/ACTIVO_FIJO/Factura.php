<?php


namespace App\Models\ACTIVO_FIJO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Factura extends Model
{
    protected $connection = 'sci';
    protected $table = 'facturas';
    public $primaryKey = 'idFactura';
    protected $fillable = [
    ];
}