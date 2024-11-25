<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Repositories\CONTROLRECURSOS\ComprobacionRepository as Repository;
use DateTime;
use DateTimeZone;

class ComprobacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Comprobacion $model
     */
    public function __construct(Comprobacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $fecha_inicial = new DateTime($data["fecha_inicial"]);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha_final = new DateTime($data["fecha_final"]);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));

        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d H:i:s");
        $data['fecha_final'] = $fecha_final->format("Y-m-d H:i:s");

        return $this->repository->registrar($data);
    }
}
