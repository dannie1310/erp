<?php


namespace App\Services\CADECO\ControlObra;


use App\Models\CADECO\AvanceObra;
use App\Repositories\CADECO\ControlObra\AvanceObraRepository as Repository;

class AvanceObraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AvanceObraService constructor.
     * @param AvanceObra $model
     */
    public function __construct(AvanceObra $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
