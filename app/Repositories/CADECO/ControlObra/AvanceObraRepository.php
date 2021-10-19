<?php


namespace App\Repositories\CADECO\ControlObra;


use App\Models\CADECO\AvanceObra;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class AvanceObraRepository extends Repository implements RepositoryInterface
{
    public function __construct(AvanceObra $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }
}
