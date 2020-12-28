<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\SubcontratosCM\Solicitud as Model;
use App\Repositories\CADECO\SubcontratosCM\SolicitudRepository as Repository;

class SolicitudCambioService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

}
