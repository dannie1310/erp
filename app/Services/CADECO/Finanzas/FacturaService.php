<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Repositories\Repository;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FacturaService constructor.
     * @param Factura $model
     */
    public function __construct(Factura $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function autorizadas(){
        $empresa = $this->repository->all();
        return Empresa::query()->whereIn('id_empresa', $empresa->pluck('id_empresa')->toArray())->get();
    }

}

