<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:18 PM
 */

namespace App\Services\CADECO\Contabilidad;



use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaMaterial;
use App\Models\CADECO\Obra;
use App\Repositories\Repository;

class CuentaMaterialService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaMaterialService constructor.
     *
     * @param CuentaMaterial $model
     */
    public function __construct(CuentaMaterial $model)
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
            throw new \Exception("No es posible registrar la cuaenta debido a que no se ha configurado el formato de cuentas de la obra.", 400);
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
}