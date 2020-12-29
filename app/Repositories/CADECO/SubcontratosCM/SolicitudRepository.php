<?php


namespace App\Repositories\CADECO\SubcontratosCM;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\CADECO\SubcontratosCM\Solicitud as Model;


class SolicitudRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
