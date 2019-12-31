<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 08:19 p. m.
 */


namespace App\Services\SCI;


use App\Models\SCI\Marca;
use App\Repositories\Repository;

class MarcaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MarcaService constructor
     * @param Marca $model
     */

    public function __construct(Marca $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
