<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacion;
use App\Repositories\Repository;

class NotificacionREPService
{
    protected $repository;

    public function __construct(RepNotificacion $model)
    {
        $this->repository = new Repository($model);
    }
    
    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}
