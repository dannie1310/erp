<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 08:37 p. m.
 */


namespace App\Services\CADECO;


use App\Models\CADECO\SolicitudCompraPartida;
use App\Repositories\Repository;

class SolicitudCompraPartidaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCompraPartidaService constructor
     * @param SolicitudCompraPartida $model
     */

    public function __construct(SolicitudCompraPartida $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data)
    {
        $this->repository->create($data);
    }

}
