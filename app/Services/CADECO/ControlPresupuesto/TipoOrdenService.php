<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 03:45 PM
 */

namespace App\Services\CADECO\ControlPresupuesto;

use App\Repositories\Repository;
use App\Models\CADECO\ControlPresupuesto\TipoOrden;

class TipoOrdenService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCambio constructor.
     *
     * @param TipoOrden $model
     */
    public function __construct(TipoOrden $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}