<?php


namespace App\Repositories\CADECO\SubcontratosCM;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\CADECO\SolicitudCambioSubcontrato as Model;


class SolicitudCambioSubcontratoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($solicitud, $archivo, $partidas)
    {
        return $this->model->registrar($solicitud, $archivo, $partidas);
    }

}
