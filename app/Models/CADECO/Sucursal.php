<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.sucursales';
    protected $primaryKey = 'id_sucursal';

    public $timestamps = false;
}