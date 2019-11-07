<?php


namespace App\Models\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class TipoAreaSolicitante extends Model
{
    protected $connection = 'seguridad';
    protected  $table = 'SEGURIDAD_ERP.Compras.ctg_areas_solicitantes';

    public function scopeUsuario($query,$user_id)
    {
        $usuario = Usuario::query()->find($user_id);
        return $usuario->areasSolicitantes();
    }
}
