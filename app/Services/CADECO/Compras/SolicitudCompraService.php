<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\SolicitudCompra;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\Repository;

class SolicitudCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCompraService constructor.
     * @param SolicitudCompra $model
     */
    public function __construct(SolicitudCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        dd("Aca", $data);
    }

    public function pdfCotizacion($id)
    {
        $pdf = new CotizacionTablaComparativaFormato($id);
        return $pdf;
    }
}