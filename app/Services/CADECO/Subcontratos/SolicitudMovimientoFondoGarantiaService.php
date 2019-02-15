<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 06/02/2019
 * Time: 06:16 PM
 */

namespace App\Services\CADECO\Subcontratos;


use App\Models\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia;
use App\Repositories\CADECO\SubcontratosFG\SolicitudMovimientoFondoGarantia\Repository;

class SolicitudMovimientoFondoGarantiaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudMovimientoFondoGarantiaService constructor.
     * @param SolicitudMovimientoFondoGarantia $model
     */
    public function __construct(SolicitudMovimientoFondoGarantia $model)
    {
        $this->repository = new Repository($model);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function cancelar(array $data, $id)
    {
        return $this->repository->cancelar($data, $id);
    }

    public function rechazar(array $data, $id)
    {
        return $this->repository->rechazar($data, $id);
    }

    public function autorizar(array $data, $id)
    {
        return $this->repository->autorizar($data, $id);
    }

    public function revertirAutorizacion(array $data, $id)
    {
        return $this->repository->revertirAutorizacion($data, $id);
    }

}