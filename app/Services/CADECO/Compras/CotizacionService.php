<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\CotizacionCompra;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\CADECO\Compras\Cotizacion\Repository;

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

    public function descargaLayout($id)
    {
        return $this->repository->descargaLayout($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->actualizar($data);
    }

    public function delete(array $data, $id)
    {
        return $this->repository->show($id)->eliminar($data);
    }
}
