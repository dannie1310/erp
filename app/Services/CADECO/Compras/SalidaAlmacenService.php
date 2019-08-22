<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\SalidaAlmacen;
use App\Repositories\Repository;

class SalidaAlmacenService
{

    /**
     * @var $repository
     */
    protected $repository;

    public function __construct(SalidaAlmacen $model)
    {
        $this->repository = new Repository($model);
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
        return $this->show($id)->eliminar($data['data'][0]);
    }
}