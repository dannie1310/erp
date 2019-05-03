<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:18 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaGeneral;
use App\Models\CADECO\Obra;
use App\Repositories\Repository;

class CuentaGeneralService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaGeneralService constructor.
     * @param CuentaGeneral $model
     */
    public function __construct(CuentaGeneral $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function store(array $data)
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
}