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

    public function store($data)
    {
        $this->conexionAcarreos();
        return $this->repository->create($data);
    }

    public function show($id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id);
    }

    public function activar($id)
    {
        $this->conexionAcarreos();
        $origen = $this->show($id);
        if ($origen->Estatus == 1) {
            abort(400, "El origen se encuentra " . $origen->estado_format . " previamente.");
        }
        return $origen->activar();
    }

    public function desactivar(array  $data, $id)
    {
        $this->conexionAcarreos();
        $origen = $this->show($id);
        if ($origen->Estatus == 0) {
            abort(400, "El origen se encuentra " . $origen->estado_format . " previamente.");
        }
        return $origen->desactivar($data['motivo']);
    }

    public function update(array $data, $id)
    {
        $this->conexionAcarreos();
        return $this->repository->show($id)->editar($data);
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
