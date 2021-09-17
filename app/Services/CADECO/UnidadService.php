<?php

namespace App\Services\CADECO;


use App\Models\CADECO\Unidad;
use App\Repositories\CADECO\Unidad\Repository;
use Illuminate\Support\Facades\DB;

class UnidadService
{

    /**
     * @var Repository
     */
    protected $repository;


    /**
     * UnidadService constructor
     * @param Unidad $model
     */

    public function __construct(Unidad $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
    public function show($id)
    {
        return $this->repository->buscar($id);
    }

    public function paginate($data)
    {
        if(isset($data['descripcion']))
        {
            return $this->repository->where([['descripcion','like', '%'.$data['descripcion'].'%']])->paginate();
        }
        return $this->repository->paginate();
    }

    public function store(array $data)
    {
        $datos = [
            'unidad' => $data['unidad'],
            'descripcion' => $data['descripcion']
        ];

        return $this->repository->create($datos);
    }

    public function update($data, $id)
    {
        $this->show($id)->actualizarUnidad($data['params']);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminarUnidad();

    }

    public function globales($data)
    {
        return $this->repository->buscarPorBase($data['base']);
    }
}
