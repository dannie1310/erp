<?php


namespace App\Repositories\ACARREOS\Camion;


use App\Models\ACARREOS\Camion;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Camion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
