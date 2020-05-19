<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:18 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\IncidentesPolizas;


use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\IncidentesPolizas\IncidenteIndividualConsolidada as Model;

class IncidenteIndividualConsolidadaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getListaEmpresasConsolidadoras()
    {
        return Empresa::consolidadora()->conComponentes()->get();
    }

    public function guardaRelacionPolizas($datos_relacion)
    {
        RelacionPolizas::create($datos_relacion);
    }

    public function guardaRelacionMovimientos($datos_relacion)
    {
        return RelacionMovimientos::create($datos_relacion);
    }
}