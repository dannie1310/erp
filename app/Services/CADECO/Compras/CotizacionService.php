<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\CotizacionCompra;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\Repository;

class CotizacionService
{
    /**
     * @var $repository
     */
    protected $repository;

    public function __construct(CotizacionCompra $model)
    {
        $this->repository = new Repository($model);
    }
}