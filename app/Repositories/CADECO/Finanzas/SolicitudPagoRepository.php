<?php


namespace App\Repositories\CADECO\Finanzas;


use App\Models\CADECO\Solicitud;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;



class SolicitudPagoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Solicitud $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }


}
