<?php


namespace App\Models\SEGURIDAD_ERP;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class UsuarioAreaCompradora extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Compras.areas_compradoras_usuario';

    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_area_compradora',
    ];

    public function asignar($data)
    {
        $usuarioArea = Usuario::query()->find($data['user_id']);
        $usuario = Usuario::query()->find('user_id');
        $area = $data['area_id'];
        $usuarioArea->areasCompradoras()->detach($usuarioArea->areasCompradoras()->pluck('id_area_compradora')->toArray());
        if($area =! [])
        {
            foreach ($data['area_id'] as $area_id)
            {
            $datos = [
                    'id_usuario' => $data['user_id'],
                    'id_area_compradora' => $area_id,
                    ];
                UsuarioAreaCompradora::query()->create($datos);
                 }
        }
        return true;
    }
}

