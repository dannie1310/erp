<?php


namespace App\Repositories\CADECO\SubcontratosCM;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\CADECO\SubcontratosCM\Transaccion as Model;


class TransaccionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
