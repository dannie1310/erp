<?php

namespace App\Services\CADECO\Contabilidad;



use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaCosto;
use App\Models\CADECO\Obra;
use App\Repositories\Repository;

class CuentaCostoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaCostoService constructor.
     *
     * @param CuentaCosto $model
     */
    public function __construct(CuentaCosto $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function store($data)
    {
        try {
            $obra = Obra::query()->find(Context::getIdObra());

            if ($obra->datosContables) {
                if ($obra->datosContables->FormatoCuenta) {
                    return $this->repository->create($data);
                }
            }
            throw new \Exception("No es posible registrar la cuenta debido a que no se ha configurado el formato de cuentas de la obra.", 400);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }
}