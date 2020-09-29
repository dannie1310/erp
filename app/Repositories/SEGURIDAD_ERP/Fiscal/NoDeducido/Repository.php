<?php


namespace App\Repositories\SEGURIDAD_ERP\Fiscal\NoDeducido;

use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Fiscal\NoDeducido as Model;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    public function __construct(Model $model)
{
    parent::__construct($model);
    $this->model = $model;
}

    public function create(array $data)
{
    return $this->model->registrar($data);
}
}
