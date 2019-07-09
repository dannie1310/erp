<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/05/2019
 * Time: 10:35 AM
 */

namespace App\Services\MODULOSSAO;


use App\Models\MODULOSSAO\ControlRemesas\Remesa;
use App\Repositories\Repository;

class RemesaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RemesaService constructor.
     * @param Remesa $model
     */
    public function __construct(Remesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id){
        return $this->repository->show($id);
    }
}