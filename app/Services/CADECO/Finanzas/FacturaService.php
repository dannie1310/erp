<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Repositories\CADECO\Finanzas\RegistrarPago\Repository;

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

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function autorizadas(){
        return $this->repository->autorizadas();
    }

    public function pendientesPago($id){
        return $this->repository->pendientesPago($id);
    }
}

