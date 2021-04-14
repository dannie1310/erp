<?php

namespace App\Repositories\SEGURIDAD_ERP\Documentacion;


use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoArchivo as Model;

class CtgTipoArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
