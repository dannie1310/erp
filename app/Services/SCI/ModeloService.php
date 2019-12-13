<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 08:18 PM
 */


namespace App\Services\SCI;


use App\Models\SCI\Modelo;
use App\Repositories\Repository;

class ModeloService
{
    /**
     * @var Repository
     */
    protected $repository;


    /**
     * ModeloService constructor
     * @param Modelo $model
     */

    public function __construct(Modelo $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {

        return $this->repository->all($data);
    }

}
