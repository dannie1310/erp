<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\Sindicato;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class SindicatoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SindicatoService constructor.
     * @param Sindicato $model
     */
    public function __construct(Sindicato $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $this->conexionAcarreos();
        return $this->repository->all($data);
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
