<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Cotizacion;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\Repository;

class CotizacionService
{
    /**
     * @var $repository
     */
    protected $repository;

    public function __construct(Cotizacion $model)
    {
        $this->repository = new Repository($model);
    }
}