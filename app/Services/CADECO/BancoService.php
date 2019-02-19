<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/02/2019
 * Time: 03:10 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Banco;
use App\Repositories\Repository;

class BancoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * BancoService constructor.
     *
     * @param Banco $model
     */
    public function __construct(Banco $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
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