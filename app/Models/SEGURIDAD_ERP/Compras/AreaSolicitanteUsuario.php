<?php


namespace App\Models\SEGURIDAD_ERP\Compras;


use Illuminate\Database\Eloquent\Model;

class AreaSolicitanteUsuario extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Compras.areas_solicitantes_usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
