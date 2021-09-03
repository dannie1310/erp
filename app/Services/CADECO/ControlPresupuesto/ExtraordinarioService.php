<?php

namespace App\Services\CADECO\ControlPresupuesto;

use App\Models\CADECO\ControlPresupuesto\Extraordinario;
use App\Repositories\CADECO\ControlPresupuesto\ExtraordinarioRepository;
use App\Repositories\Repository;
use App\PDF\ControlPresupuesto\VariacionVolumenFormato;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;

class ExtraordinarioService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VariacionVolumen constructor.
     *
     * @param VariacionVolumen $model
     */
    public function __construct(Extraordinario $model)
    {
        $this->repository = new ExtraordinarioRepository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }


    public function delete($data, $id)
    {
        return $this->repository->show($id)->rechazar($data['data'][0]);
    }

    public function store(array $data)
    {

        $solicitud_variacion_volumen = $this->repository->create($data);

        return $solicitud_variacion_volumen;
    }

    public function autorizar($id){
        return $this->repository->show($id)->autorizar();
    }

    public function pdf($id)
    {
        $pdf = new VariacionVolumenFormato($id);
        return $pdf;
    }
}
