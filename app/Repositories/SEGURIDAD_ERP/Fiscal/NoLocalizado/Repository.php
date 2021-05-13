<?php


namespace App\Repositories\SEGURIDAD_ERP\Fiscal\NoLocalizado;

use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado as Model;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
