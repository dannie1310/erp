<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 12:59 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Fondo;
use App\Repositories\Repository;

class FondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FondoService constructor.
     *
     * @param Fondo $model
     */
    public function __construct(Fondo $model)
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

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }

}