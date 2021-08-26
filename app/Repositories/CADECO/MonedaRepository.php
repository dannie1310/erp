<?php


namespace App\Repositories\CADECO;


use App\Models\CADECO\Moneda;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class MonedaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Moneda $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function buscarPorBase($data)
    {
        return $this->model->buscarPorBase($data);
    }
}
