<?php

namespace App\Repositories\CADECO;

use App\Models\CADECO\Transaccion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class TransaccionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Transaccion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
