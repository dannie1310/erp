<?php

namespace App\Repositories\SEGURIDAD_ERP\ChatBot;

use App\Models\SEGURIDAD_ERP\ChatBot\Peticion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;


class PeticionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Peticion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }


}
