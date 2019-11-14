<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:01 p. m.
 */


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Repositories\Repository;

class SolicitudComplementoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudComplementoService constructor
     *
     * @param SolicitudComplemento $model
     */

    public function __construct(SolicitudComplemento $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

}
