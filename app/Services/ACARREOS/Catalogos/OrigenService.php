<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\Origen;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class OrigenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * OrigenService constructor.
     * @param Origen $model
     */
    public function __construct(Origen $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $this->conexionAcarreos();
        return  $this->repository->paginate($data);
    }

    private function conexionAcarreos()
    {
        try{
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database',Proyecto::pluck('base_datos')->first());
        }catch (\Exception $e){
            abort(500, "El proyecto no se encuentra activo en acarreos.");
            throw $e;
        }
    }
}
