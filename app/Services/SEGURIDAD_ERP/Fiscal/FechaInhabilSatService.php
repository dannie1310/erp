<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;

use App\Repositories\Repository;
use App\Models\SEGURIDAD_ERP\Fiscal\FechaInhabilSat;
use DateTime;
use DateTimeZone;

class FechaInhabilSatService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FechaInhabilSatService constructor.
     * @param FechaInhabilSat $model
     */
    public function __construct(FechaInhabilSat $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function eliminar($id){
        $item = $this->repository->show($id);
        $item->eliminar();
        return $item;
    }

    public function store($data)
    {
        /** EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
         * */
        $fecha = New DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha"] = $fecha->format("Y/m/d");
        return $this->repository->create($data);
    }
}
