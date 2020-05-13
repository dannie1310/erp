<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\IncidentesPolizas;

use App\Models\SEGURIDAD_ERP\IncidentesPolizas\IncidenteIndividualConsolidada as Model;
use App\Repositories\SEGURIDAD_ERP\IncidentesPolizas\IncidenteIndividualConsolidadaRepository as Repository;

class IncidenteIndividualConsolidadaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    private function store($datos)
    {
        $incidente = $this->repository->store($datos);
        return $incidente;
    }

    public function buscarDiferencias($parametros)
    {
        return [];
    }

}