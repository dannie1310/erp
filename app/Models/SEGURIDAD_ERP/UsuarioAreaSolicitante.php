<?php


namespace App\Models\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class UsuarioAreaSolicitante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Compras.areas_solicitantes_usuario';

    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_area_solicitante',
    ];

    public function asignar($data)
    {
        $usuarioArea = Usuario::query()->find($data['user_id']);
        $usuario = Usuario::query()->find('user_id');
        $area = $data['area_id'];
        $usuarioArea->areasSolicitantes()->detach($usuarioArea->areasSolicitantes()->pluck('id_area_solicitante')->toArray());
        if($area =! [])
        {
            foreach ($data['area_id'] as $area_id)
            {
                $datos = [
                    'id_usuario' => $data['user_id'],
                    'id_area_solicitante' => $area_id,
                ];
                UsuarioAreaSolicitante::query()->create($datos);
            }
        }
        return true;
    }
}
