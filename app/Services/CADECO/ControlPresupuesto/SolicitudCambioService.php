<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:45 PM
 */

namespace App\Services\CADECO\ControlPresupuesto;

use App\Repositories\Repository;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;

class SolicitudCambioService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCambio constructor.
     *
     * @param SolicitudCambio $model
     */
    public function __construct(SolicitudCambio $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}