<?php


namespace App\Models\SEGURIDAD_ERP\Compras;


use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Compras.configuracion';

    public $timestamps = false;
}
