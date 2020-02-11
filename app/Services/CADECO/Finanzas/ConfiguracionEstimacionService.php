<?php


namespace App\Services\CADECO\Finanzas;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Repositories\Repository;


class ConfiguracionEstimacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConfiguracionEstimacionService constructor.
     * @param ConfiguracionEstimacion $model
     */
    public function __construct(ConfiguracionEstimacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data){
        return $this->repository->create($data['data']);
    }

    public function index()
    {
        return  $this->repository->all();
    }
}
