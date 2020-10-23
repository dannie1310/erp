<?php


namespace App\Repositories\ACARREOS\Tiro;


use App\Models\ACARREOS\Tiro;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class TiroRepository extends Repository implements RepositoryInterface
{
    public function __construct(Tiro $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }
}
