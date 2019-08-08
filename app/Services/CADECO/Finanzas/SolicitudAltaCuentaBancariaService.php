<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/08/2019
 * Time: 05:11 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use App\Repositories\Repository;

class SolicitudAltaCuentaBancariaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudAltaCuentaBancariaService constructor.
     * @param SolicitudAlta $model
     */
    public function __construct(SolicitudAlta $model)
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
}