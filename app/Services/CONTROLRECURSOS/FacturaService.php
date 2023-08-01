<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Factura;
use App\Repositories\CONTROLRECURSOS\FacturaRepository as Repository;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Factura $model
     */
    public function __construct(Factura $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['concepto']))
        {
            $this->repository->where([['Concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        return $this->repository->paginate($data);
    }
}
