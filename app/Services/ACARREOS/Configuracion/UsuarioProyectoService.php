<?php


namespace App\Services\ACARREOS\Configuracion;

use Illuminate\Support\Facades\DB;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Repositories\ACARREOS\SCAConfiguracion\UsuarioProyecto\Repository;

class UsuarioProyectoService{
    
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CamionService constructor.
     * @param UsuarioProyecto $model
     */
    public function __construct(UsuarioProyecto $model)
    {
        $this->repository = new Repository($model);
    }

    public function getChecadores($data)
    {
        $id_checador = array_key_exists('id_checador', $data)?$data['id_checador']:null;
        $this->conexionAcarreos();
        $id_proyecto = Proyecto::pluck('id_proyecto')->first();
        return  $this->repository->getChecadores($id_proyecto, $id_checador);
    }

    private function conexionAcarreos()
    {
        $base_datos = Proyecto::pluck('base_datos')->first();
        if(!is_null($base_datos))
        {
            try {
                DB::purge('acarreos');
                \Config::set('database.connections.acarreos.database', $base_datos);
            } catch (\Exception $e) {
                abort(500, "El proyecto no se encuentra activo en acarreos.");
                throw $e;
            }
        }else{
            abort(500, "El proyecto no se encuentra activo en acarreos.");
        }
    }
}